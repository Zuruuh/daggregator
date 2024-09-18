from fastapi import FastAPI, HTTPException
from fastapi.responses import StreamingResponse
from twscrape import API
import os
import json
from loguru import logger


db = "accounts.db"
with open(db, "w+") as handle:
    logger.info('DB OK')
app = FastAPI()
api = API(debug=True, pool=db, raise_when_no_account=True)


@app.get("/")
def health_check():
    return {"Hello": "World"}


async def stream_tweets(tweets):
    async for tweet in tweets:
        logger.info("received tweet " + tweet.id_str)
        yield (
            json.dumps(
                {
                    "id": tweet.id,
                    "author": tweet.user.username,
                    "title": tweet.rawContent,
                    "photos": list(map(lambda photo: photo.url, tweet.media.photos)),
                    "videos": list(
                        map(
                            lambda video: sorted(
                                video.variants,
                                reverse=True,
                                key=lambda variant: variant.bitrate,
                            )[0].url,
                            tweet.media.videos,
                        )
                    ),
                }
            ).encode()
            + b"\n"
        )


setup_done = False


async def setup():
    global setup_done
    global api

    if setup_done:
        return

    setup_done = True
    await api.pool.add_account(
        username=os.environ.get("TWITTER_USERNAME"),
        password=os.environ.get("TWITTER_PASSWORD"),
        email=os.environ.get("TWITTER_EMAIL"),
        email_password="...?",
        cookies=os.environ.get("TWITTER_COOKIES").strip("'"),
        mfa_code=os.environ.get("TWITTER_OTP_CODE")
    )


@app.get("/tweets")
async def get_tweets(limit: int):
    global api
    await setup()
    user_id = int(os.environ.get("TWITTER_USER_ID", "0").strip('"'))
    if user_id == 0:
        raise HTTPException(status_code=500, detail="No twitter user id set!")

    tweets = api.liked_tweets(user_id, limit=limit)

    return StreamingResponse(stream_tweets(tweets), media_type="application/x-ndjson")
