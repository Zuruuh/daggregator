#syntax=docker/dockerfile:1.10-labs

FROM alpine:3.20

WORKDIR /srv

RUN apk add curl python3 && \
    adduser -D app && chown -R app:app /srv

USER app

ENV PATH="$PATH:/home/app/.cargo/bin"

RUN curl --proto '=https' --tlsv1.2 -LsSf https://github.com/astral-sh/uv/releases/download/0.4.10/uv-installer.sh | sh && \
    uv venv

ENV VIRTUAL_ENV="/srv/.venv"
ENV PATH="$PATH:/srv/.venv/bin"

COPY requirements.txt .

RUN --mount=type=cache,target=/root/.cache/uv uv pip sync requirements.txt

COPY main.py .

EXPOSE 8000/tcp

HEALTHCHECK --interval=60s --timeout=3s --start-period=3s --retries=3 CMD [ "curl", "0.0.0.0:8000" ]

CMD ["uvicorn", "main:app", "--host", "0.0.0.0"]
