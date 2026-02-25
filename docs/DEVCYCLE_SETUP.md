# Development Cycle Setup

## Setup Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/senih25/dijital-sosyal-hak-platformu.git
   cd dijital-sosyal-hak-platformu
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Configure your environment:
   - Create a `.env` file in the root directory and add your configuration settings.

## API Key Configuration
To use the API, you'll need to configure your API keys:
1. Go to your project settings on the platform you are integrating with.
2. Obtain the API keys and insert them into your `.env` file as follows:
   ```
   API_KEY=your_api_key_here
   ```

## Feature Flag Creation Guide
To create a feature flag:
1. Navigate to the feature flags management section of the platform.
2. Click on ‘Create New Feature Flag’.
3. Fill out the details:
   - **Name**: A descriptive name for the feature.
   - **Key**: A unique key identifier.
   - **Type**: Choose between boolean, multivariate, etc.
4. Save the feature flag.

## Troubleshooting
- **Issue**: API key not working
  **Solution**: Ensure your API key is correctly set in the `.env` file and that there are no extra spaces or lines.

- **Issue**: Dependencies not installing
  **Solution**: Check your internet connection and ensure Node.js is properly installed.

If you encounter issues not covered here, refer to the platform's documentation or seek help from the community.