/**
 * Main Chatbot JavaScript
 * This file handles all the client-side functionality for the chatbot interface
 * including user input processing, API calls, and response rendering
 */

document.addEventListener('DOMContentLoaded', () => {
    // Get references to DOM elements
    const userInput = document.getElementById('userInput');
    const generateBtn = document.getElementById('generateBtn');
    const questionsContainer = document.getElementById('questionsContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Attach main event listener
    generateBtn.addEventListener('click', handleUserInput);

    /**
     * Toggle loading state of the interface
     * @param {boolean} show - Whether to show or hide loading state
     */
    function showLoading(show) {
        if (loadingIndicator) {
            loadingIndicator.style.display = show ? 'block' : 'none';
        }
        generateBtn.disabled = show;
    }

    /**
     * Main handler for processing user input and generating questions
     * Makes API call to backend and handles response/error display
     */
    async function handleUserInput() {
        const text = userInput.value.trim();
        if (!text) {
            alert('Please enter your question or text!');
            return;
        }

        // Show loading state
        showLoading(true);

        // Clear previous response and show thinking message
        questionsContainer.innerHTML = '<p class="placeholder-text">Thinking...</p>';

        try {
            // Prepare the message based on the input
            let message = text;
            if (!text.toLowerCase().includes('generate questions') && 
                !text.toLowerCase().includes('create questions') && 
                !text.endsWith('?')) {
                message = `Generate meaningful questions from this text: ${text}`;
            }

            // Call the API
            const response = await fetch('chat_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(JSON.stringify({
                    message: data.error,
                    details: data.details || 'No additional details available'
                }));
            }

            // Clear the container
            questionsContainer.innerHTML = '';

            // Format and display the response
            const responseLines = data.response.split('\n');
            responseLines.forEach(line => {
                if (line.trim()) {
                    const responseElement = document.createElement('div');
                    responseElement.className = 'question-item';
                    responseElement.textContent = line;
                    questionsContainer.appendChild(responseElement);
                }
            });

        } catch (error) {
            let errorMessage, errorDetails;
            try {
                const errorData = JSON.parse(error.message);
                errorMessage = errorData.message;
                errorDetails = errorData.details;
            } catch {
                errorMessage = error.message;
                errorDetails = 'Please try again or contact support if the problem persists.';
            }

            questionsContainer.innerHTML = `
                <div class="question-item error">
                    <strong>Error:</strong> ${errorMessage}
                    <br><br>
                    <em>${errorDetails}</em>
                </div>
            `;
        } finally {
            showLoading(false);
        }
    }

    /**
     * Checks if the input text is a question based on common patterns
     * @param {string} text - The input text to check
     * @returns {boolean} - True if text appears to be a question
     */
    function isQuestion(text) {
        // Check if the input is a question
        return text.trim().endsWith('?') || 
               text.toLowerCase().startsWith('what') ||
               text.toLowerCase().startsWith('how') ||
               text.toLowerCase().startsWith('why') ||
               text.toLowerCase().startsWith('when') ||
               text.toLowerCase().startsWith('where') ||
               text.toLowerCase().startsWith('can you') ||
               text.toLowerCase().startsWith('could you');
    }

    /**
     * Displays a response to a direct question in the chat interface
     * @param {string} question - The user's question
     */
    function respondToQuestion(question) {
        const response = generateResponse(question);
        const responseElement = document.createElement('div');
        responseElement.className = 'question-item';
        responseElement.textContent = response;
        questionsContainer.appendChild(responseElement);
    }

    /**
     * Generates appropriate responses for common questions about the system
     * @param {string} question - The user's question
     * @returns {string} - The generated response
     */
    function generateResponse(question) {
        // Basic response generation logic
        question = question.toLowerCase();
        
        if (question.includes('how to generate questions')) {
            return "To generate questions, simply paste your text or paragraph here and I'll create relevant questions from it. You can also specify how many questions you want, like 'generate 5 questions from this text'.";
        }
        
        if (question.includes('what can you do')) {
            return "I'm an AI assistant focused on generating questions from text and helping with educational content. I can:\n1. Generate questions from any text you provide\n2. Create specific types of questions (what, where, how, etc.)\n3. Answer questions about the question generation process\n4. Help you understand how to use this tool effectively";
        }

        // Default response for other questions
        return "I'm primarily designed to generate questions from text. If you'd like me to create questions, please provide a paragraph or specify what type of questions you need. For example, you can say 'generate questions from this text: [your text]' or 'create 5 questions about [topic]'.";
    }

    /**
     * Main question generation logic for input text
     * Breaks text into sentences and generates different types of questions
     * @param {string} text - The input text to generate questions from
     */
    function generateQuestionsFromText(text) {
        // Simple question generation logic
        const sentences = text.split(/[.!?]+/).filter(sentence => sentence.trim().length > 0);
        let questions = [];
            
        sentences.forEach(sentence => {
            sentence = sentence.trim();
            if (sentence.length < 4) return;

            let question = '';
                
            // Create different types of questions based on sentence structure
            if (sentence.toLowerCase().includes('is') || sentence.toLowerCase().includes('are')) {
                question = createYesNoQuestion(sentence);
            } else if (sentence.toLowerCase().includes('in') || sentence.toLowerCase().includes('at')) {
                question = createWhereQuestion(sentence);
            } else {
                question = createWhatQuestion(sentence);
            }

            if (question) {
                questions.push(question);
            }
        });

        // Display generated questions
        if (questions.length > 0) {
            const introElement = document.createElement('div');
            introElement.className = 'question-item';
            introElement.textContent = "Here are the questions I've generated from your text:";
            questionsContainer.appendChild(introElement);

            questions.forEach((question, index) => {
                const questionElement = document.createElement('div');
                questionElement.className = 'question-item';
                questionElement.textContent = `${index + 1}. ${question}`;
                questionsContainer.appendChild(questionElement);
            });
        } else {
            const errorElement = document.createElement('div');
            errorElement.className = 'question-item';
            errorElement.textContent = "I couldn't generate questions from this text. Could you please provide a more detailed paragraph? The text should contain complete sentences for better question generation.";
            questionsContainer.appendChild(errorElement);
        }
    }

    /**
     * Creates a yes/no question from a statement
     * @param {string} sentence - The input sentence
     * @returns {string} - The generated yes/no question
     */
    function createYesNoQuestion(sentence) {
        const words = sentence.split(' ');
        if (words.length < 3) return '';
        
        const beVerb = words.find(word => 
            word.toLowerCase() === 'is' || 
            word.toLowerCase() === 'are'
        );
        
        if (beVerb) {
            const index = words.findIndex(word => 
                word.toLowerCase() === beVerb.toLowerCase()
            );
            
            return `${beVerb.charAt(0).toUpperCase() + beVerb.slice(1)} ${words.slice(0, index).join(' ')} ${words.slice(index + 1).join(' ')}?`;
        }
        return '';
    }

    /**
     * Creates a "where" question from a statement
     * @param {string} sentence - The input sentence
     * @returns {string} - The generated where question
     */
    function createWhereQuestion(sentence) {
        return `Where ${sentence.toLowerCase().includes('is') ? 'is' : 'are'} ${sentence.replace(/^(in|at)\s+/i, '').replace(/\.$/, '')}?`;
    }

    /**
     * Creates a "what" question from a statement
     * @param {string} sentence - The input sentence
     * @returns {string} - The generated what question
     */
    function createWhatQuestion(sentence) {
        const words = sentence.split(' ');
        if (words.length < 3) return '';
        return `What ${words.slice(1).join(' ')}?`;
    }
}); 