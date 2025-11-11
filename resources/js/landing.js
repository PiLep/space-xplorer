// Landing page dynamic effects
document.addEventListener('DOMContentLoaded', function() {
    // Create floating particles
    function createParticles() {
        const container = document.querySelector('.bg-textured');
        if (!container) return;
        
        const particleCount = 6; // Reduced from 15 to 6 for more dispersed effect
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random position and delay
            const left = Math.random() * 100;
            const delay = Math.random() * 20;
            const duration = 15 + Math.random() * 10;
            
            particle.style.left = `${left}%`;
            particle.style.animationDelay = `${delay}s`;
            particle.style.animationDuration = `${duration}s`;
            
            // Random size (smaller)
            const size = 1 + Math.random() * 1.5;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            container.appendChild(particle);
        }
    }
    
    // Typewriter effect for terminal messages
    function typewriterEffect(element, text, speed = 50, callback) {
        if (!element) return;
        
        const originalText = element.textContent || text;
        element.textContent = '';
        element.style.borderRight = '2px solid rgba(0, 255, 136, 0.7)';
        
        let i = 0;
        const timer = setInterval(() => {
            if (i < originalText.length) {
                element.textContent += originalText.charAt(i);
                i++;
            } else {
                clearInterval(timer);
                element.style.borderRight = 'none';
                element.classList.add('typewriter-complete');
                if (callback) callback();
            }
        }, speed);
    }
    
    // Fake loading sequence
    function showLoadingMessages(container) {
        const messages = [
            { text: '[INIT] Connecting to STELLAR network...', delay: 0, type: 'info' },
            { text: '[OK] Authentication successful', delay: 800, type: 'success' },
            { text: '[LOAD] Decrypting recruitment portal...', delay: 1600, type: 'info' },
            { text: '[OK] Portal access granted', delay: 2400, type: 'success' },
            { text: '[LOAD] Loading corporate data...', delay: 3200, type: 'info' },
            { text: '[OK] System ready', delay: 4000, type: 'success' },
        ];
        
        messages.forEach(({ text, delay, type }) => {
            setTimeout(() => {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'text-xs opacity-0';
                messageDiv.style.transition = 'opacity 0.4s ease-in';
                
                if (type === 'success') {
                    messageDiv.className += ' text-space-primary dark:text-space-primary';
                } else {
                    messageDiv.className += ' text-gray-500 dark:text-gray-500';
                }
                
                messageDiv.textContent = text;
                container.appendChild(messageDiv);
                
                // Fade in
                setTimeout(() => {
                    messageDiv.style.opacity = '1';
                }, 50);
            }, delay);
        });
    }
    
    // Initialize landing page sequence
    function initLandingSequence() {
        const promptContainer = document.getElementById('terminal-prompt');
        const loadingContainer = document.getElementById('loading-messages');
        const mainContent = document.getElementById('main-content');
        
        if (!promptContainer || !loadingContainer || !mainContent) return;
        
        // Step 1: Type the terminal prompt
        const promptText = 'HR@STELLAR:~$ ';
        const commandText = 'access_recruitment_portal';
        
        typewriterEffect(promptContainer, promptText, 30, () => {
            // Add command after prompt
            setTimeout(() => {
                const commandSpan = document.createElement('span');
                commandSpan.className = 'text-space-primary dark:text-space-primary';
                commandSpan.style.borderRight = '2px solid rgba(0, 255, 136, 0.7)';
                promptContainer.appendChild(commandSpan);
                
                typewriterEffect(commandSpan, commandText, 40, () => {
                    // Step 2: Show loading messages
                    loadingContainer.style.opacity = '1';
                    loadingContainer.style.transition = 'opacity 0.5s';
                    showLoadingMessages(loadingContainer);
                    
                    // Step 3: After 5 seconds, reveal main content in same flow
                    setTimeout(() => {
                        // Get all loading messages
                        const loadingMessages = loadingContainer.querySelectorAll('div');
                        const messageCount = loadingMessages.length;
                        
                        // Calculate animation timing
                        const staggerDelay = 100; // ms between each message fade out
                        const fadeOutDuration = 700; // ms per message animation
                        
                        // Prepare main content for smooth animation
                        mainContent.style.willChange = 'opacity, transform';
                        mainContent.style.transition = 'opacity 1.4s cubic-bezier(0.4, 0, 0.2, 1), transform 1.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        
                        // Start main content reveal simultaneously with first message fade
                        // This creates the effect of content rising as messages disappear
                        requestAnimationFrame(() => {
                            setTimeout(() => {
                                mainContent.style.opacity = '1';
                                mainContent.style.transform = 'translateY(0)';
                            }, 50);
                        });
                        
                        // Fade out loading messages with upward animation (staggered)
                        loadingMessages.forEach((msg, index) => {
                            setTimeout(() => {
                                // Prepare for animation
                                msg.style.willChange = 'opacity, transform';
                                msg.style.transition = 'opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1), transform 0.7s cubic-bezier(0.4, 0, 0.2, 1)';
                                
                                // Trigger animation on next frame for smoothness
                                requestAnimationFrame(() => {
                                    msg.style.opacity = '0';
                                    msg.style.transform = 'translateY(-12px)';
                                });
                                
                                // Remove element after animation completes
                                setTimeout(() => {
                                    msg.style.pointerEvents = 'none';
                                    msg.style.height = '0';
                                    msg.style.marginBottom = '0';
                                    msg.style.overflow = 'hidden';
                                }, fadeOutDuration);
                            }, index * staggerDelay);
                        });
                        
                        // Animate prompt container upward progressively as messages disappear
                        const promptContainerEl = document.getElementById('terminal-prompt-container');
                        if (promptContainerEl) {
                            // Prepare container
                            promptContainerEl.style.willChange = 'opacity, transform';
                            
                            // Start moving container up smoothly
                            setTimeout(() => {
                                requestAnimationFrame(() => {
                                    promptContainerEl.style.transition = 'transform 1.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1)';
                                    promptContainerEl.style.transform = 'translateY(-25px)';
                                    promptContainerEl.style.opacity = '0.15';
                                    
                                    // After all animations complete, hide container completely
                                    setTimeout(() => {
                                        promptContainerEl.style.pointerEvents = 'none';
                                        promptContainerEl.style.visibility = 'hidden';
                                    }, Math.max(1600, (messageCount * staggerDelay) + fadeOutDuration + 200));
                                });
                            }, 150);
                        }
                        
                        // Initialize rest of effects
                        initTypewriter();
                        addGlitchToLogo();
                        addStatusIndicator();
                    }, 5000);
                });
            }, 300);
        });
    }
    
    // Initialize typewriter effects for main content
    function initTypewriter() {
        const messages = [
            { selector: '[data-typewriter="classified"]', text: '[CLASSIFIED] STELLAR CORPORATION - RECRUITMENT PORTAL', delay: 300 },
            { selector: '[data-typewriter="status"]', text: '[STATUS] ACTIVE RECRUITMENT', delay: 900 },
            { selector: '[data-typewriter="tagline"]', text: 'Discover. Extract. Expand.', delay: 1500, isTagline: true },
        ];
        
        messages.forEach(({ selector, text, delay, isTagline }) => {
            const element = document.querySelector(selector);
            if (element) {
                // Store original text if it exists
                if (!element.textContent && text) {
                    element.textContent = text;
                }
                const finalText = element.textContent || text;
                if (finalText) {
                    setTimeout(() => {
                        if (isTagline) {
                            // Special handling for tagline with progressive highlight and rotation
                            animateTagline(element, finalText);
                        } else {
                            typewriterEffect(element, finalText, 40);
                        }
                    }, delay);
                }
            }
        });
    }
    
    // Animate tagline with progressive highlight and rotation
    function animateTagline(element, text) {
        // First, write the text with typewriter effect
        typewriterEffect(element, text, 40, () => {
            // After typewriter completes, transform into word spans and animate
            const fullText = element.textContent;
            const words = fullText.split(' ').map(word => word.trim()).filter(word => word);
            
            // Clear and recreate with spans
            element.innerHTML = '';
            const wordSpans = [];
            
            words.forEach((word, index) => {
                const span = document.createElement('span');
                span.className = 'tagline-word';
                span.textContent = word;
                span.style.display = 'inline-block';
                span.style.transition = 'color 1.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease-out, text-shadow 1.5s ease-out';
                span.style.marginRight = '0.3em';
                span.style.color = 'inherit'; // Start with inherited color
                element.appendChild(span);
                wordSpans.push(span);
                
                // Add space after word (except last)
                if (index < words.length - 1) {
                    element.appendChild(document.createTextNode(' '));
                }
            });
            
            // Progressively highlight each word with warning color
            wordSpans.forEach((span, index) => {
                setTimeout(() => {
                    // Add highlighted class and inline styles
                    span.classList.add('highlighted');
                    span.style.color = 'rgb(255, 170, 0)'; // warning color #ffaa00
                    
                    // Add subtle scale effect on highlight
                    span.style.transform = 'scale(1.08)';
                    setTimeout(() => {
                        span.style.transform = 'scale(1)';
                    }, 400);
                }, 500 + (index * 1000)); // Stagger each word highlight (1 second between each)
            });
        });
    }
    
    // Add glitch effect to logo periodically
    function addGlitchToLogo() {
        const logo = document.querySelector('h1');
        if (!logo) return;
        
        // Add glitch class after initial delay
        setTimeout(() => {
            logo.classList.add('glitch-subtle');
        }, 2000);
    }
    
    // Add status indicator pulse
    function addStatusIndicator() {
        const statusElement = document.querySelector('[data-status="active"]');
        if (!statusElement) return;
        
        const indicator = document.createElement('span');
        indicator.className = 'status-indicator';
        statusElement.insertBefore(indicator, statusElement.firstChild);
    }
    
    // Initialize all effects
    createParticles();
    initLandingSequence();
});

