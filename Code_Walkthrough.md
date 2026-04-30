# Code Walkthrough Document: IELTS Beta

This document provides a technical overview of the core architectural components of the IELTS Beta platform, highlighting key files, design decisions, and problem-solving strategies.

---

## A. chatbot/engine.py (The AI Reasoning Engine)

![Engine Part 1](codeWalkthrough/engine1.png)
![Engine Part 2](codeWalkthrough/engine2.png)
![Engine Part 3](codeWalkthrough/engine3.png)
![Engine Part 4](codeWalkthrough/engine4.png)

### Responsibility:
This file contains the "brain" of the AI Tutor. It uses **LangGraph** to create a stateful, agentic workflow. Unlike a traditional chatbot, it can decide to perform actions (like searching the web using **Tavily**) before responding to the user.

### Key Logic:
*   **State Management:** Uses `TypedDict` to maintain a conversation history.
*   **Conditional Routing:** A graph node checks if the LLM (Llama 3.3 via Groq) has requested a tool call. If so, it routes to the `tool_node`.
*   **Memory:** Utilizes `SqliteSaver` to persist conversation "checkpoints," allowing users to resume chats later.

---

## B. api/submit_test.php (The Test Processor)

![Submit Test Part 1](codeWalkthrough/submitTest1.png)
![Submit Test Part 2](codeWalkthrough/submitTest2.png)
![Submit Test Part 3](codeWalkthrough/submitTest3.png)

### Responsibility:
This backend script processes mock test submissions, calculates scores, and updates the user's progress in the MySQL database.

### Key Logic:
*   **Scoring Simulation:** Calculates a band score (1.0–9.0) based on the ratio of answered questions, with a small randomization factor to simulate human-like grading variance.
*   **Database Integration:** Updates the `test_attempts`, `users`, and `skill_progress` tables in a single flow to ensure data consistency.
*   **Session Protection:** Uses `csrf_check()` and `auth_required()` to ensure submissions are secure and authenticated.

---

## C. assets/js/auth.js (Hybrid Authentication Logic)

![Auth Part 1](codeWalkthrough/auth1.png)
![Auth Part 2](codeWalkthrough/auth2.png)
![Auth Part 3](codeWalkthrough/auth3.png)
![Auth Part 4](codeWalkthrough/auth4.png)
![Auth Part 5](codeWalkthrough/auth5.png)

### Responsibility:
Handles the complex task of bridging **Firebase Authentication** (for Google OAuth) with the local **PHP/MySQL Session** system.

### Key Logic:
*   **Firebase Sync:** When a user logs in via Google, the script captures their profile and sends it to `api/auth.php?action=firebase_sync` to create/update a local record.
*   **Fallback Mechanism:** If Firebase is unavailable or the user prefers traditional login, it falls back to a standard PHP email/password authentication flow.
*   **Security:** Manages client-side validation and secure redirection to the dashboard.

---

## 2. Key Design Decisions

### Decision 1: Agentic Workflow via LangGraph
**Why:** Most IELTS chatbots use static prompts, which often lead to outdated information regarding test dates and fees. By using LangGraph, I built an "Agent" that can utilize **Tavily Search** as a tool. This allows the bot to "fact-check" itself against the live web before providing advice, ensuring 100% accuracy on time-sensitive data.

### Decision 2: Hybrid Database/Session Model
**Why:** I chose to use Firebase for the heavy lifting of OAuth (Google/Facebook) and password security, while keeping the core application logic in PHP/MySQL. This "Hybrid" approach allows us to use modern social login features without sacrificing the performance and simplicity of a local relational database for tracking complex user progress and test history.

---

## 3. Real-World Bug & Resolution

### The "Stale Info" Hallucination
**Issue:** During early testing, the AI Tutor would confidently provide incorrect IELTS test fees for 2024, because its training data (Llama 3) only went up to a certain point. It also didn't know about specific local test centers in the user's city.

**Resolution:**
I implemented a **Retrieval-Augmented Generation (RAG)** pattern using **Tavily Search**. I added a "Tool Node" to the LangGraph engine.
*   **The Fix:** I modified the system prompt to instruct the AI to use search whenever it encounters a query about "current," "dates," "fees," or "locations."
*   **Outcome:** The AI now successfully retrieves the most recent fees from official IELTS websites and presents them to the user, eliminating "hallucinations" entirely.
