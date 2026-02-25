# Use a minimal base image
FROM python:3.9-alpine

# Set a non-root user
RUN addgroup -S myusergroup && adduser -S myuser -G myusergroup
USER myuser

# Set the working directory
WORKDIR /app

# Install dependencies (include verification if applicable)
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the application code
COPY --chown=myuser:myuser . .

# Define security headers if applicable
# (This part depends on the application framework)
# Example for Flask: 
# from flask import Flask
# app = Flask(__name__)
# app.config['HTTP_X_CONTENT_TYPE_OPTIONS'] = 'nosniff'

# Start the application
CMD ["python", "app.py"]