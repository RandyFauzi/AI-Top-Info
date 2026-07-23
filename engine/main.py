import os
import re
import json
import requests
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from dotenv import load_dotenv
from langchain_google_genai import ChatGoogleGenerativeAI
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import JsonOutputParser

load_dotenv()

app = FastAPI(title="AI Top Info - LangChain Engine")

class OpportunityRequest(BaseModel):
    raw_content: str

class OpportunityResponse(BaseModel):
    is_relevant_opportunity: bool
    title: str
    summary: str
    source_platform: str
    source_url: str
    contacts: dict

def search_tavily(query: str) -> str:
    """Helper to query Tavily Search API with fallback."""
    api_key = os.getenv("TAVILY_API_KEY")
    if not api_key:
        raise HTTPException(
            status_code=400,
            detail="TAVILY_API_KEY is missing. Real-time web search requires an active search key."
        )

    try:
        response = requests.post(
            "https://api.tavily.com/search",
            json={
                "api_key": api_key,
                "query": query,
                "search_depth": "basic",
                "include_answer": True
            },
            timeout=10
        )
        if response.status_code == 200:
            results = response.json()
            if results.get("answer"):
                return results["answer"]
            snippets = [r.get("content", "") for r in results.get("results", [])]
            return "\n\n".join(snippets[:3])
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Tavily search connection failed: {str(e)}"
        )
        
    raise HTTPException(status_code=500, detail="Search failed with unknown response.")

@app.post("/analyze-opportunity", response_model=OpportunityResponse)
async def analyze_opportunity(request: OpportunityRequest):
    api_key = os.getenv("GEMINI_API_KEY")
    if not api_key or not api_key.startswith("AIzaSy"):
        raise HTTPException(
            status_code=400,
            detail="Missing or mock GEMINI_API_KEY. Configured API key must be a valid Google AI Studio key starting with 'AIzaSy'."
        )
        
    try:
        # Initialize Gemini via LangChain
        llm = ChatGoogleGenerativeAI(model="gemini-pro", google_api_key=api_key)

        # ----------------------------------------------------
        # AGENT 1: Researcher / Extractor Agent
        # ----------------------------------------------------
        researcher_prompt = ChatPromptTemplate.from_messages([
            ("system", """You are an Elite B2B Lead Opportunity Aggregator.
Scan the raw text signal and extract a specific job/dataset opportunity.
Look for:
- Title of the opportunity or job opening.
- Summary of what they are looking for (e.g. video datasets, annotation services, computer vision engineer).
- Source platform (e.g. 'LinkedIn', 'Discord', 'Web').
- Source URL (Extract the direct link to the original post/message. If no URL is present, extract or guess one from their domain).
- Extracted contacts: email and clean whatsapp/phone.
Determine if the post is a relevant opportunity for sourcing dataset licensing, computer vision training, or generative AI.
Return your output strictly as a JSON object with keys:
is_relevant_opportunity (boolean),
title (string),
summary (string),
source_platform (string: 'LinkedIn', 'Discord', or 'Web'),
source_url (string),
contacts (object with keys: email, phone_wa)."""),
            ("human", "{signal}")
        ])
        
        research_chain = researcher_prompt | llm | JsonOutputParser()
        result = research_chain.invoke({"signal": request.raw_content})

        return {
            "is_relevant_opportunity": bool(result.get("is_relevant_opportunity", True)),
            "title": str(result.get("title", "Opportunity Alert")),
            "summary": str(result.get("summary", "")),
            "source_platform": str(result.get("source_platform", "Web")),
            "source_url": str(result.get("source_url", "https://example.com")),
            "contacts": result.get("contacts", {"email": None, "phone_wa": None})
        }
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"LangChain Gemini Agent analysis failed: {str(e)}"
        )

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8001, reload=True)
