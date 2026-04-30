# IELTS Beta — AI Tutor (FastAPI + LangGraph)

## Setup

1. **Install dependencies:**
   ```bash
   cd chatbot
   pip install -r requirements.txt
   ```

2. **Set API keys:**
   ```bash
   cp .env.example .env
   # Edit .env and add your Groq and Tavily API keys
   ```

3. **Start the server:**
   ```bash
   # From the project root (ielts_beta_v3/)
   uvicorn chatbot.api:app --reload --port 8000
   ```

4. **Test it's working:**
   - Open: http://localhost:8000/health
   - Should return: `{"status":"ok","engine":"IELTSGraphEngine",...}`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/health` | Check server status |
| POST | `/chat` | Send a message, get AI reply |
| DELETE | `/chat/{thread_id}` | Reset a conversation |

## Chat request format
```json
POST /chat
{
  "message": "How do I improve my Writing Task 2?",
  "thread_id": "user_1_20260426"
}
```

## Integration
The PHP page `pages/chatbot.php` automatically connects to `http://localhost:8000`.
Make sure this server is running before testing the chatbot page.
