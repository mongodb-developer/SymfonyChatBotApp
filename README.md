# Symfony ChatBot Application with MongoDB and RAG

This repository contains the code for building a chatbot application using the Symfony framework, MongoDB Atlas, Voyage AI, and OpenAI. The chatbot is designed to answer questions based on the Symfony Documentation, Doctrine ORM, and Doctrine ODM documentation. It utilizes the Retrieval Augmented Generation (RAG) architecture to provide accurate and context-aware responses.

## Overview

This chatbot application leverages the following technologies:

-   **Symfony:** Backend framework for managing API communication and handling user queries.
-   **MongoDB Atlas:** Database for storing chunked documentation and vector embeddings. Uses Atlas's vector search for retrieving relevant content.
-   **Voyage AI:** Used to generate vector embeddings from the documentation chunks.
-   **OpenAI:** Provides the language model for generating context-aware responses.
-   **Twig:** Frontend templating engine for creating the user interface.
-   **LLPhant:** PHP generative AI framework used for chunking documents.

## Prerequisites

Before you begin, ensure you have the following installed and configured:

-   MongoDB Atlas account and cluster (free tier available)
-   PHP 8.1 or above
-   Symfony 7 or above
-   Voyage AI API key
-   OpenAI API key

## Setup

1.  **Clone the repository:**

    ```bash
    git clone <repository_url>
    cd SymfonyDocsChatBot
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    ```

3.  **Install the MongoDB extension:**

    ```bash
    pecl install mongodb
    ```

4.  **Configure environment variables:**

    Create a `.env` file in the project root and add your API keys and other configurations:

    ```
    VOYAGE_API_KEY=<Your_Voyage_AI_API_Key>
    VOYAGE_ENDPOINT=[https://api.voyageai.com/v1/embeddings](https://api.voyageai.com/v1/embeddings)
    OPENAI_API_KEY=<Your_OpenAI_API_Key>
    OPENAI_API_URL=[https://api.openai.com/v1/chat/completions](https://api.openai.com/v1/chat/completions)
    BATCH_SIZE=32
    MAX_RETRIES=3
    ```

5.  **Clone documentation repositories:**

    ```bash
    git clone https://github.com/symfony/symfony-docs.git
    git clone https://github.com/doctrine/mongodb-odm.git
    git clone https://github.com/doctrine/orm.git
    ```

6.  **Copy RST files to the `public` directory:**

    Copy the content of `symfony-docs/docs`, `mongodb-odm/docs`, and `orm/docs` into the `public` directory of your Symfony project.

7.  **Create chunks and store them into MongoDB:**

    ```bash
    php bin/console app:create-chunks
    ```

8.  **Generate embeddings for the chunks:**

    ```bash
    php -d memory_limit=2G bin/console app:embed-chunks
    ```

9.  **Create a Vector Search Index in MongoDB Atlas:**

    Navigate to your MongoDB Atlas cluster, go to the "Search" tab, and create a vector search index on the collection where you stored the chunks. Configure the index path to `contentEmbedding` and the similarity method (e.g., `cosine`).

## Running the Application

Start the Symfony development server:

```bash
symfony server:start
```
