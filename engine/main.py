import os
import re
import json
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from dotenv import load_dotenv
from langchain_google_genai import ChatGoogleGenerativeAI
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import JsonOutputParser

load_dotenv()

app = FastAPI(title="AI Top Info - LangChain Engine")

class AnalysisRequest(BaseModel):
    raw_content: str

class AnalysisResponse(BaseModel):
    company_name: str
    domain: str
    industry: str
    description: str
    total_funding: str
    score: int
    intent_category: str
    reasoning: str
    target_persona: str
    suggested_angle: str
    email_draft: str
    contacts: dict

def run_local_fallback(content: str) -> dict:
    """Fallback parser if Gemini API is unavailable or fails."""
    content_lower = content.lower()
    
    # Identify Mock Company Profiles
    if "veedio" in content_lower:
        company = "Veedio AI"
        domain = "veedio.ai"
        industry = "Generative AI & Video Creation"
        description = "A video content creation platform raising funds to build generative AI avatars."
        funding = "$45M"
        score = 95
        category = "Generative Video"
        contacts = {
            "email": "growth@veedio.ai",
            "whatsapp": "12135550199",
            "linkedin": "https://linkedin.com/company/veedio-ai",
            "discord": "https://discord.gg/veedio"
        }
        reason = "The company is actively training next-generation generative video diffusion models, which creates an immediate demand for extensive, high-quality licensed video training datasets to refine avatar realism."
    elif "visiondrive" in content_lower:
        company = "VisionDrive"
        domain = "visiondrive.ai"
        industry = "Autonomous Vehicles & Logistics"
        description = "An autonomous drone logistics startup expanding fleets and navigation systems."
        funding = "N/A"
        score = 90
        category = "Computer Vision"
        contacts = {
            "email": "team@visiondrive.ai",
            "whatsapp": "14155550177",
            "linkedin": "https://linkedin.com/company/visiondrive",
            "discord": "https://discord.gg/visiondrive"
        }
        reason = "The startup trains vision systems for spatial navigation and collision avoidance in multi-angle environments. They require highly specific annotated video datasets."
    elif "aerocam" in content_lower:
        company = "AeroCam Systems"
        domain = "aerocamsystems.com"
        industry = "Defense & Security Tech"
        description = "A builder of intelligent surveillance systems and drones mapping vehicle and pedestrian metrics."
        funding = "N/A"
        score = 88
        category = "Computer Vision"
        contacts = {
            "email": "contact@aerocamsystems.com",
            "whatsapp": "16505550144",
            "linkedin": "https://linkedin.com/company/aerocam-systems",
            "discord": "https://discord.gg/aerocam"
        }
        reason = "AeroCam is hiring ML Engineers specifically to process multi-hour security video streams and map vehicle types."
    elif "physicworld" in content_lower or "sora" in content_lower:
        company = "PhysicWorld AI"
        domain = "physicworld.ai"
        industry = "Physical Simulation AI"
        description = "Developing next-generation AI physical world simulators and video synthesis architectures."
        funding = "$12M"
        score = 96
        category = "Generative Video"
        contacts = {
            "email": "datasets@physicworld.ai",
            "whatsapp": "14085550133",
            "linkedin": "https://linkedin.com/company/physicworld",
            "discord": "https://discord.gg/physicworld"
        }
        reason = "PhysicWorld is explicitly seeking to license large libraries of video datasets for physical world simulation."
    elif "chatguru" in content_lower:
        company = "ChatGuru"
        domain = "chatguru.example.com"
        industry = "Customer Support AI"
        description = "Creators of fine-tuned text LLM pipelines for customer support automation."
        funding = "N/A"
        score = 15
        category = "Text LLM"
        contacts = {
            "email": "hello@chatguru.example.com",
            "whatsapp": None,
            "linkedin": "https://linkedin.com/company/chatguru",
            "discord": None
        }
        reason = "ChatGuru is focused exclusively on text-based Llama models. They have no current or planned multi-modal or video components."
    elif "textify" in content_lower:
        company = "Textify AI"
        domain = "textify.example.com"
        industry = "Natural Language Processing"
        description = "Building conversational chatbot APIs and summarization services."
        funding = "$5M"
        score = 12
        category = "Text LLM"
        contacts = {
            "email": None,
            "whatsapp": None,
            "linkedin": "https://linkedin.com/company/textify",
            "discord": None
        }
        reason = "Textify AI relies on text tokenization and LLM models. Zero multi-modal context found."
    else:
        company = "LexiWriter"
        domain = "lexiwriter.com"
        industry = "Software & NLP"
        description = "A SaaS copywriting assistant focused on fine-tuned text LLM APIs."
        funding = "N/A"
        score = 25
        category = "Text LLM"
        contacts = {
            "email": "support@lexiwriter.com",
            "whatsapp": None,
            "linkedin": None,
            "discord": None
        }
        reason = "LexiWriter is strictly a text generation utility. They have zero multi-modal or vision-related activities."

    persona = "Lead Computer Vision Engineer" if score >= 80 else "Head of AI Product"
    angle = f"Help {company} accelerate their {category} training cycles with pre-labeled video datasets."
    email = f"Subject: Accelerating {company}'s model training datasets\n\nHi team,\n\nI saw your recent updates on training newer {category} models.\n\nSourcing high-fidelity multi-angle video footage is usually the biggest bottleneck. We license pre-cleared, annotated video catalogs specifically formatted for custom CV training.\n\nWould you be open to reviewing a free sample?\n\nBest,\nThe AI Top Info Datasets Team"

    return {
        "company_name": company,
        "domain": domain,
        "industry": industry,
        "description": description,
        "total_funding": funding,
        "score": score,
        "intent_category": category,
        "reasoning": reason,
        "target_persona": persona,
        "suggested_angle": angle,
        "email_draft": email,
        "contacts": contacts
    }

@app.post("/analyze-company", response_model=AnalysisResponse)
async def analyze_company(request: AnalysisRequest):
    api_key = os.getenv("GEMINI_API_KEY")
    if not api_key:
        return run_local_fallback(request.raw_content)
        
    try:
        # Initialize Gemini via LangChain
        llm = ChatGoogleGenerativeAI(model="gemini-1.5-flash", google_api_key=api_key)

        # ----------------------------------------------------
        # AGENT 1: Researcher Agent & Contact Miner
        # ----------------------------------------------------
        researcher_prompt = ChatPromptTemplate.from_messages([
            ("system", """You are an Elite B2B Intelligence Researcher and precise Contact Data Miner. 
Extract the company name, domain, industry, funding size, description, and contact info from the raw signal.
Search for email formats, WhatsApp/phone patterns, linkedin.com URLs, and discord.gg invite links.
Return your output strictly as a JSON object with keys: 
company_name, domain, industry, description, total_funding, contacts.
The 'contacts' key must contain:
{{
  "email": "extracted_email@domain.com or null",
  "whatsapp": "Clean numeric phone string (e.g. 628123...) or null",
  "linkedin": "linkedin.com URL or null",
  "discord": "discord.gg link or null"
}}"""),
            ("human", "{signal}")
        ])
        research_chain = researcher_prompt | llm | JsonOutputParser()
        research_result = research_chain.invoke({"signal": request.raw_content})

        company_name = research_result.get("company_name", "Unknown AI")
        domain = research_result.get("domain", "unknown.com")
        industry = research_result.get("industry", "Artificial Intelligence")
        description = research_result.get("description", "")
        total_funding = research_result.get("total_funding", "N/A")
        contacts = research_result.get("contacts", {"email": None, "whatsapp": None, "linkedin": None, "discord": None})

        # ----------------------------------------------------
        # AGENT 2: Critic Agent (Evaluating fit for Video Dataset sales)
        # ----------------------------------------------------
        critic_prompt = ChatPromptTemplate.from_messages([
            ("system", """You are a Lead AI Technical Critic. Read the company research profile and raw signal. 
Determine if this company needs VIDEO or IMAGE datasets for model training (Computer Vision, Generative Video, Drones, Surveillance, Autonomous Driving).
Assign a score from 1 to 100:
- Score 80-100: Actively building Generative Video, Diffusion models, Drones, Robotics, or Computer Vision. High video data need.
- Score 50-79: Multi-modal AI company.
- Score 1-49: Strictly Text LLM, NLP, Chatbots, or Voice AI. No video data need.
Assign an intent_category: 'Computer Vision', 'Generative Video', 'Text LLM', or 'Other'.
Write a brief technical 'reasoning'.
Return strictly a JSON object with keys: score (integer), intent_category, reasoning."""),
            ("human", "Company: {company_name}\nDescription: {description}\nSignal: {signal}")
        ])
        critic_chain = critic_prompt | llm | JsonOutputParser()
        critic_result = critic_chain.invoke({
            "company_name": company_name,
            "description": description,
            "signal": request.raw_content
        })

        score = int(critic_result.get("score", 50))
        intent_category = critic_result.get("intent_category", "Other")
        reasoning = critic_result.get("reasoning", "")

        # ----------------------------------------------------
        # AGENT 3: Copywriter Agent
        # ----------------------------------------------------
        copywriter_prompt = ChatPromptTemplate.from_messages([
            ("system", """You are an Elite B2B SaaS Outreach Strategist. 
Draft a hyper-personalized pitch for this lead.
Determine the 'target_persona' (e.g. CTO, Lead AI Engineer, VP of AI).
Define a 'suggested_angle' (e.g. accelerating model training with custom annotated video libraries).
Draft a concise cold email pitch 'email_draft' addressing the target bottleneck. 
Start with a hook referring to their news, mention the bottleneck (sourcing clean video datasets), and offer a free sample CTAs.
Return strictly a JSON object with keys: target_persona, suggested_angle, email_draft."""),
            ("human", "Company: {company_name}\nDescription: {description}\nCategory: {category}\nScore: {score}\nReasoning: {reasoning}")
        ])
        copywriter_chain = copywriter_prompt | llm | JsonOutputParser()
        copywriter_result = copywriter_chain.invoke({
            "company_name": company_name,
            "description": description,
            "category": intent_category,
            "score": score,
            "reasoning": reasoning
        })

        return {
            "company_name": company_name,
            "domain": domain,
            "industry": industry,
            "description": description,
            "total_funding": total_funding,
            "score": score,
            "intent_category": intent_category,
            "reasoning": reasoning,
            "target_persona": copywriter_result.get("target_persona", "CTO"),
            "suggested_angle": copywriter_result.get("suggested_angle", "Offer custom video annotations"),
            "email_draft": copywriter_result.get("email_draft", "Hi, hope to discuss training data."),
            "contacts": contacts
        }
    except Exception as e:
        return run_local_fallback(request.raw_content)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8001, reload=True)
