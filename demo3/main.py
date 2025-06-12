from fastapi import FastAPI
from fastapi.responses import JSONResponse
import time

app = FastAPI()

@app.get("/")
def read_root():
    return {"message": "Hello from FastAPI behind Nginx!"}

@app.get("/api")
def api():
    time.sleep(1)  # giả lập xu lý nặng
    return JSONResponse(
        content={"message": "Processed by FastAPI via /api"},
        headers={"X-Custom": "fastapi"}
    )