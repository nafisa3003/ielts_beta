import os
import sqlite3
from typing import TypedDict, Annotated, List
from dotenv import load_dotenv
load_dotenv()

from langgraph.graph import StateGraph, START, END
from langgraph.graph.message import add_messages
from langgraph.checkpoint.sqlite import SqliteSaver

from langchain_core.messages import BaseMessage, HumanMessage, AIMessage, SystemMessage, ToolMessage
from langchain_groq import ChatGroq
from langchain_community.tools.tavily_search import TavilySearchResults as TavilySearch

# 1. THE DATA MODEL
# This defines what the AI remembers during a conversation.
class ChatState(TypedDict):
    # 'messages' is a list that stores everything me and the bot say.
    # 'add_messages' ensures that new messages are added to the list instead of replacing it.
    messages: Annotated[list[BaseMessage], add_messages]

# 2. THE KNOWLEDGE BASE
# This function reads 'knowledge.json' file. 
def load_knowledge_base():
    import json
    try:
        with open("knowledge.json", "r", encoding="utf-8") as f:
            return json.load(f)
    except:
        # If the file is missing, use these defaults so the bot doesn't crash.
        return {
            "Audio Lab": "Practice listening materials and strategies",
            "Draftroom": "Writing practice and feedback section",
            "Studio": "Speaking practice environment"
        }

# 3. THE BRAIN (IELTSGraphEngine)
class IELTSGraphEngine:
    def __init__(self):
        # Initialize the LLM (Groq Llama 3)
        self.llm = ChatGroq(
            api_key=os.getenv("GROQ_API_KEY"),
            model="llama-3.3-70b-versatile"
        )

        # Initialize the Search Tool (Tavily)
        self.tavily = TavilySearch(
            api_key=os.getenv("TAVILY_API_KEY")
        )

        #'bind' the search tool to the AI so the AI knows it is allowed to use it.
        self.tools = [self.tavily]
        self.llm_with_tools = self.llm.bind_tools(self.tools)

        # Load our local knowledge
        self.kb = load_knowledge_base()

        # Set up the 'Memory' (SQLite database)
        # check_same_thread=False allows the UI and the AI logic to talk to the DB at the same time.
        self.db_conn = sqlite3.connect("checkpoint.db", check_same_thread=False)
        self.memory = SqliteSaver(self.db_conn)

        # Build the 'Flowchart' (Graph) of how the AI should work.
        self.graph = self._build_graph()

    # NODE: SEARCHING
    # This part runs ONLY if the AI decides it needs to look something up online.
    def tool_node(self, state: ChatState):
        last_msg = state["messages"][-1]
        results = []
        # If the AI asked to use a tool, we run the tool (Tavily) here.
        for tool_call in last_msg.tool_calls:
            if tool_call["name"] in ["tavily_search_results_json", "tavily_search_results"]:
                query = tool_call["args"]["query"]
                print(f"DEBUG: Searching Tavily for: {query}")
                res = self.tavily.invoke(query)
                # We package the search results into a 'ToolMessage' to send back to the AI.
                results.append(ToolMessage(content=str(res), tool_call_id=tool_call["id"]))
        return {"messages": results}

    # NODE: CHATTING
    # This is the main part where the AI generates a reply.
    def chat_node(self, state: ChatState):
        # This prompt tells the AI exactly who it is and how to behave.
        system_prompt = f"""
    You are IELTS Beta, an advanced AI tutor specializing in Writing Task 2 feedback.
    
    CORE STRENGTHS:
    - Detailed Writing Task 2 feedback using official IELTS rubrics.
    - Advanced logical reasoning via LangGraph.
    - Real-time research via Tavily search for test dates and fees.
    - Context-aware scoring based on our internal Knowledge Base: {self.kb}
    
    INSTRUCTIONS:
    - Provide structured feedback with headings and bullet points.
    - Use Tavily if you need real-time data.
    - Be strict but encouraging with band scores (0-9).
    """
        # Combine the instructions with the actual conversation history.
        messages = [SystemMessage(content=system_prompt)] + state["messages"]

        print("Sending to LLM...")
        # The AI looks at everything and decides to either 'Talk' or 'Search'.
        response = self.llm_with_tools.invoke(messages)
        return {"messages": [response]}

    # BUILDING THE FLOWCHART
    def _build_graph(self):
        builder = StateGraph(ChatState)

        # Add our logic 'rooms' (Nodes)
        builder.add_node("chat", self.chat_node)
        builder.add_node("tools", self.tool_node)

        # Start the conversation at the 'chat' node.
        builder.set_entry_point("chat")

        # After the AI talks, check: Did it ask to search?
        def route_after_chat(state):
            last_msg = state["messages"][-1]
            if hasattr(last_msg, "tool_calls") and last_msg.tool_calls:
                return "tools" # Go to search
            return END # Finish and show response to user

        # Connect the nodes based on the decision above.
        builder.add_conditional_edges("chat", route_after_chat)
        # After searching, go back to 'chat' so the AI can summarize the results.
        builder.add_edge("tools", "chat")

        # Compile the flowchart with memory.
        return builder.compile(checkpointer=self.memory)

    # STARTING THE ENGINE
    # This is the function the UI calls to get an answer.
    def run(self, user_input: str, thread_id: str = "default"):
        # The 'thread_id' helps the bot remember DIFFERENT conversations with DIFFERENT people.
        config = {"configurable": {"thread_id": thread_id}}
        
        # We start the flowchart and wait for the final message.
        result = self.graph.invoke(
            {"messages": [HumanMessage(content=user_input)]},
            config=config
        )

        # Return the very last message the AI generated.
        return result["messages"][-1].content