import os
import pandas as pd
from googleapiclient.discovery import build
import json

# API Key
API_KEY = "AIzaSyBIgjJU052xRI-38_iyk1IKQAeVOF0T5J0" # gilbert
# API_KEY = "AIzaSyCIvy1l2aAyWH_oM-oTLQqNgUYt9BddoJE" # james

# Define the search query
query = input('Enter the search query : ')

# Build the YouTube API client
youtube = build("youtube", "v3", developerKey=API_KEY)

# Call the search.list method to retrieve the search results
search_response = youtube.search().list(
    q=query,
    type="video",
    part="id,snippet",
    maxResults=10
).execute()

# Get the video details for each video in the search results
video_details = []
for video in search_response["items"]:
    video_id = video["id"]["videoId"]
    video_response = youtube.videos().list(
        id=video_id,
        part="statistics,snippet"
    ).execute()
    channel_response = youtube.channels().list(
        id=video_response["items"][0]["snippet"]["channelId"],
        part="statistics"
    ).execute()
    video_details.append({
        "title": video["snippet"]["title"],
        "url": f"https://www.youtube.com/watch?v={video_id}",
        "view_count": video_response["items"][0]["statistics"]["viewCount"],
        "like_count": video_response["items"][0]["statistics"].get("likeCount",0),
        "subscriber_count": channel_response["items"][0]["statistics"]["subscriberCount"],
        "published_at": video_response["items"][0]["snippet"]["publishedAt"],
    })

# Create a pandas DataFrame from the video details
df = pd.DataFrame(video_details)

# Save the results to a CSV file
df.to_csv(query+"_results.csv", encoding='UTF-8-sig', index=False)