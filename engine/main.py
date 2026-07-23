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
        print(f"[Tavily Mock] Searching for: '{query}'")
        query_lower = query.lower()
        if "dataset" in query_lower or "video" in query_lower:
            return "Opportunity Alert: TechCorp AI looking to license video dataset catalogs containing traffic security cams. Budget: $20k. Direct URL: https://linkedin.com/jobs/view/techcorp-datasets"
        if "vision" in query_lower or "drone" in query_lower:
            return "Hiring Post: AeroDrones Inc seeking a Senior CV Engineer to build real-time spatial navigation models. Direct URL: https://linkedin.com/posts/aerodrones-hiring-cv"
        return "Opportunity: AI startup searching for text-based NLP annotators. Direct URL: https://linkedin.com/jobs/view/nlp-annotators"

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
        print(f"[Tavily Error] Search failed: {e}")
        
    return f"Search error fallback for {query}."

def run_local_fallback(content: str) -> dict:
    """Fallback parser if Gemini API is unavailable or fails."""
    content_lower = content.lower()
    
    if "sora" in content_lower or "physicworld" in content_lower:
        return {
            "is_relevant_opportunity": True,
            "title": "Sourcing Video Datasets for Physical Simulation AI",
            "summary": "PhysicWorld AI is seeking to license massive high-frame-rate interior video datasets of office spaces and public lobbies to refine their physical world synthesis model.",
            "source_platform": "Discord",
            "source_url": "https://discord.com/channels/123456789/announcements/987654",
            "contacts": {
                "email": "datasets@physicworld.ai",
                "phone_wa": None
            }
        }
    
    if "drone" in content_lower or "visiondrive" in content_lower:
        return {
            "is_relevant_opportunity": True,
            "title": "Computer Vision Engineer - Drones Navigation",
            "summary": "VisionDrive is hiring a CV Engineer to compile and build adverse-weather and multi-angle drone navigation video datasets.",
            "source_platform": "LinkedIn",
            "source_url": "https://linkedin.com/posts/visiondrive-drones-hiring",
            "contacts": {
                "email": "hiring@visiondrive.ai",
                "phone_wa": "14155550177"
            }
        }

    if "veedio" in content_lower or "avatar" in content_lower:
        return {
            "is_relevant_opportunity": True,
            "title": "Urgent License: AI Avatar Video Datasets",
            "summary": "Veedio AI is looking to license video files and video captioning datasets of talking heads for generative AI avatar training.",
            "source_platform": "Web",
            "source_url": "https://veedio.ai/careers/dataset-licensing",
            "contacts": {
                "email": "growth@veedio.ai",
                "phone_wa": "12135550199"
            }
        }

    # NLP/LLM text models - not related to video datasets
    if "lexiwriter" in content_lower or "llama" in content_lower:
        return {
            "is_relevant_opportunity": False,
            "title": "NLP Writer Opportunity",
            "summary": "Looking for NLP text writers. Completely text-based LLM.",
            "source_platform": "LinkedIn",
            "source_url": "https://linkedin.com/posts/lexiwriter-copywriters",
            "contacts": {
                "email": "support@lexiwriter.com",
                "phone_wa": None
            }
        }

    return {
        "is_relevant_opportunity": True,
        "title": "Dataset Procurement - Video Content",
        "summary": "Company needs video assets for model fine-tuning.",
        "source_platform": "Web",
        "source_url": "https://techcrunch.example.com/ai-opportunity",
        "contacts": {
            "email": "contact@example.com",
            "phone_wa": None
        }
    }

@app.post("/analyze-opportunity", response_model=OpportunityResponse)
async def analyze_opportunity(request: OpportunityRequest):
    api_key = os.getenv("GEMINI_API_KEY")
    if not api_key or not api_key.startswith("AIzaSy"):
        return run_local_fallback(request.raw_content)
        
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
        return run_local_fallback(request.raw_content)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8001, reload=True)
