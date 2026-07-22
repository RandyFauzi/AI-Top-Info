<?php

declare(strict_types=1);

namespace App\Services\AIReasoning;

class PromptManager
{
    /**
     * System prompt to analyze raw B2B signals and extract company details, assign scores (1-100),
     * and determine intent category based on whether they need Video/Vision training datasets.
     */
    public function getAnalysisPrompt(string $rawContent): string
    {
        return <<<PROMPT
You are an expert B2B Lead Intelligence AI and highly precise Data Miner. Your task is to analyze the following raw text signal (from news, discord, or linkedin), extract target company details, scan meticulously for direct contact details, and return a structured JSON response.

Goal:
1. Identify the target company name, domain, industry, description, and funding.
2. Meticulously scan the raw text for contact points: emails, WhatsApp numbers (clean numeric format like '628123...' for wa.me), linkedin.com URLs, and discord.gg invite links.
3. Score the lead from 1 to 100 based on their probability of needing VIDEO or COMPUTER VISION datasets for AI model training.

Scoring Rules:
- SCORE 80-100: Active generative video models, diffusion models, autonomous driving, drone vision, robotics, or computer vision architectures needing video datasets.
- SCORE 50-79: Multi-modal AI company.
- SCORE 1-49: Strictly text-based (NLP, LLMs, Chatbots), voice, or SaaS without vision/video AI requirements.

JSON Output Format (Strictly return ONLY JSON, no markdown code blocks, no trailing comments):
{
  "company_name": "Extract company name, clean and without extra words",
  "domain": "e.g., veedio.ai (guess logically if not present, format as domain.com)",
  "industry": "e.g., Artificial Intelligence, Autonomous Vehicles, Generative AI",
  "description": "Short description of what the company does",
  "total_funding": "e.g. '$45M' or 'N/A'",
  "score": 95,
  "intent_category": "One of: 'Computer Vision', 'Generative Video', 'Autonomous Navigation', 'Text LLM', 'Audio AI', 'Other'",
  "contacts": {
    "email": "extracted_email@domain.com or null",
    "whatsapp": "Clean numeric phone string (e.g. 628123456789) or null",
    "linkedin": "https://linkedin.com/... or null",
    "discord": "https://discord.gg/... or null"
  },
  "reasoning": "Detailed technical explanation of why they got this score, and how their recent activities create an opportunity for video dataset sales."
}

Raw Text Signal to Analyze:
"""
{$rawContent}
"""
PROMPT;
    }

    /**
     * Prompt to generate custom outreach strategies and bespoke email drafts.
     */
    public function getOutreachPrompt(string $companyName, string $description, string $intentCategory, int $score, string $reasoning, string $rawSignal): string
    {
        return <<<PROMPT
You are an Elite B2B SaaS Copywriter and Sales Strategist. Your company sells premium, high-quality video training datasets (Computer Vision annotations, multi-angle driving datasets, high-frame-rate human activities, video captioning, etc.).

We want to draft a highly personalized, value-driven cold outreach email to a target persona (like CTO, VP of AI, or Lead Computer Vision Engineer) at the following company:

Company Name: {$companyName}
Company Description: {$description}
Intent Category: {$intentCategory}
Lead Score: {$score}/100
Reasoning / Opportunity: {$reasoning}
Latest Ingested Signal context: {$rawSignal}

Tasks:
1. Identify the most appropriate Target Persona (e.g. 'CTO', 'Lead Computer Vision Engineer', or 'Director of AI Research').
2. Develop a Suggested Angle (a specific hook based on their recent news/signal that explains why they need video data right now).
3. Draft a short, crisp, hyper-personalized cold outreach email that:
   - Starts with a relevant hook referring to their recent signal (e.g., congrats on funding, drone fleet expansion, or new avatar research).
   - Identifies their likely bottleneck: training vision models requires clean, annotated video datasets to avoid hallucinations or collision errors.
   - Offers a small, friction-free call to action (e.g., "Can I send over a sample dataset of 500 annotated multi-angle office videos?").
   - Strictly avoids corporate buzzwords, generic fluff, and overly formal greetings. Keep it natural and punchy.

JSON Output Format (Strictly return ONLY JSON, no markdown code blocks, no trailing comments):
{
  "target_persona": "Exact job title",
  "suggested_angle": "One-sentence pitch strategy",
  "email_draft": "Subject: [Subject Line]\\n\\nHi [First Name],\\n\\n[Email body]\\n\\nBest,\\n[My Name]"
}
PROMPT;
    }
}
