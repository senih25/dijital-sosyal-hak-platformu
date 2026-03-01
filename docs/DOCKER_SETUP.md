# Docker Setup Documentation

This document provides instructions for installing and using Docker for the project.

## Installation Instructions

1. **Install Docker Desktop**:
   - Go to the [Docker website](https://www.docker.com/products/docker-desktop) and download Docker Desktop for your operating system.
   - Follow the installation instructions provided on the website.

2. **Start Docker**:
   - After installation, open Docker Desktop and wait for it to initialize.

3. **Verify Docker Installation**:
   - Open a terminal/command prompt and run the following command:
     ```bash
     docker --version
     ```
   - You should see the installed version of Docker.

## Usage Instructions

1. **Clone the repository**:
   ```bash
   git clone https://github.com/senih25/dijital-sosyal-hak-platformu.git
   cd dijital-sosyal-hak-platformu
   ```

2. **Build the Docker Image**:
   - Inside your project directory, run:
   ```bash
   docker build -t <your-image-name> .
   ```

3. **Run the Docker Container**:
   - To run the container:
   ```bash
   docker run -d -p 80:80 <your-image-name>

## Troubleshooting

- If you encounter any issues, check the Docker documentation or seek help from the community.

## Conclusion

This concludes the Docker setup documentation for this project. Follow these instructions to get started with Docker and ensure your environment is correctly configured.
