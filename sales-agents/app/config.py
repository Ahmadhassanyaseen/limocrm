from functools import lru_cache
from pathlib import Path

from pydantic_settings import BaseSettings, SettingsConfigDict

ROOT = Path(__file__).resolve().parent.parent
REPO_ROOT = ROOT.parent
DATA_DIR = ROOT / "data"
DB_PATH = DATA_DIR / "sales.db"


class Settings(BaseSettings):
    model_config = SettingsConfigDict(
        env_file=str(ROOT / ".env"),
        env_file_encoding="utf-8",
        extra="ignore",
    )

    app_host: str = "127.0.0.1"
    app_port: int = 8787
    secret_key: str = "dev-secret-change-me"
    timezone: str = "America/New_York"
    operator_token: str = ""

    sender_name: str = "LimoGen"
    sender_email: str = "hello@example.com"
    company_legal_name: str = "LimoGen"
    mailing_address: str = "Set MAILING_ADDRESS in sales-agents/.env"
    public_base_url: str = "http://127.0.0.1:8787"

    explore_demo_base_url: str = "http://localhost/limocrm/login_explore.php"
    calendar_url: str = ""
    brochure_note: str = "I can send the module brochure if useful."

    openai_api_key: str = ""
    openai_base_url: str = "https://api.openai.com/v1"
    openai_model: str = "gpt-4o-mini"

    resend_api_key: str = ""
    smtp_host: str = ""
    smtp_port: int = 587
    smtp_user: str = ""
    smtp_password: str = ""
    smtp_starttls: bool = True

    imap_host: str = ""
    imap_port: int = 993
    imap_user: str = ""
    imap_password: str = ""
    imap_folder: str = "INBOX"

    google_places_api_key: str = ""
    hunter_api_key: str = ""

    twilio_account_sid: str = ""
    twilio_auth_token: str = ""
    twilio_from_number: str = ""
    twilio_whatsapp_from: str = ""
    meta_whatsapp_token: str = ""
    meta_whatsapp_phone_id: str = ""
    whatsapp_template_name: str = "limogen_followup"

    email_followup_days: str = "3,7,14"
    daily_email_limit: int = 40
    daily_sms_limit: int = 10
    disable_scheduler: bool = False


@lru_cache
def get_settings() -> Settings:
    DATA_DIR.mkdir(parents=True, exist_ok=True)
    return Settings()
