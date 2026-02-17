function showPage(pageId) {
            document.querySelectorAll('.page').forEach(page => {
                page.style.display = 'none';
            });
            document.getElementById(pageId + '-page').style.display = 'block';
            
            if (pageId === 'browse') {
                loadBrowseVideos();
            }
        }

        // Modal Management
        function showLoginModal() {
            document.getElementById('login-modal').style.display = 'flex';
        }

        function showRegisterModal() {
            document.getElementById('register-modal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Video Player
        function playVideo(videoId) {
            const modal = document.getElementById('video-modal');
            const player = document.getElementById('video-player');
            const title = document.getElementById('video-modal-title');
            
            // In a real application, you would fetch video data from the server
            const sampleVideos = {
                'sample1': {
                    title: 'Getting Started with StreamIt',
                    url: 'data:video/mp4;base64,', // Placeholder
                    description: 'Learn how to use StreamIt platform effectively.'
                },
                'sample2': {
                    title: 'Platform Features Overview',
                    url: 'data:video/mp4;base64,', // Placeholder
                    description: 'Comprehensive overview of all platform features.'
                },
                'sample3': {
                    title: 'Upload Your First Video',
                    url: 'data:video/mp4;base64,', // Placeholder
                    description: 'Step-by-step guide to uploading videos.'
                }
            };
            
            const video = sampleVideos[videoId];
            if (video) {
                title.textContent = video.title;
                // player.src = video.url; // Would set actual video source
                document.getElementById('video-description').innerHTML = 
                    <p><strong>Description:</strong> ${video.description}</p>;
                modal.style.display = 'flex';
            }
        }

        // Search Functionality
        function searchVideos() {
            const query = document.getElementById('searchInput').value;
            alert(`Searching for: ${query}\nIn a real application, this would search the video database.`);
        }

        function filterVideos() {
            const category = document.getElementById('categoryFilter').value;
            const search = document.getElementById('browseSearch').value;
            alert(`Filtering videos by category: ${category || 'All'}\nSearch term: ${search || 'None'}`);
        }

        // Load Browse Videos
        function loadBrowseVideos() {
            const container = document.getElementById('browse-videos');
            const videos = [
                { title: 'Getting Started with StreamIt', uploader: 'Admin', category: 'Tutorial' },
                { title: 'Platform Features Overview', uploader: 'Admin', category: 'Demo' },
                { title: 'Upload Your First Video', uploader: 'Admin', category: 'Tutorial' },
                { title: 'Advanced CSS Techniques', uploader: 'jane_smith', category: 'Educational' },
                { title: 'JavaScript ES6 Features', uploader: 'john_doe', category: 'Tutorial' },
                { title: 'Responsive Web Design', uploader: 'mike_dev', category: 'Educational' },
                { title: 'Database Design Basics', uploader: 'sarah_db', category: 'Tutorial' },
                { title: 'API Development Guide', uploader: 'alex_api', category: 'Demo' }
            ];
            
            container.innerHTML = videos.map((video, index) => 
                <div class="video-card" onclick="playVideo('video${index}')">
                    <div class="video-thumbnail">▶️</div>
                    <div class="video-info">
                        <div class="video-title">${video.title}</div>
                        <div class="video-meta">${video.uploader} • ${Math.floor(Math.random() * 7) + 1} days ago • ${video.category}</div>
                    </div>
                </div>
            ).join('');
        }

        // Form Handlers
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // In a real application, this would authenticate with the server
            if (email && password) {
                alert('Login successful! Welcome to StreamIt.');
                closeModal('login-modal');
                // Update UI to show logged-in state
                updateNavForLoggedInUser();
            }
        });

        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            // In a real application, this would register with the server
            if (username && email && password) {
                alert('Registration successful! Please login to continue.');
                closeModal('register-modal');
                showLoginModal();
            }
        });

        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const file = document.getElementById('videoFile').files[0];
            const title = document.getElementById('videoTitle').value;
            const description = document.getElementById('videoDescription').value;
            const category = document.getElementById('videoCategory').value;
            
            if (file && title && category) {
                // In a real application, this would upload to the server
alert(`Video "${title}" uploaded successfully!\nIt will be reviewed by administrators before being published.`);
                document.getElementById('upload-form').reset();
            }
        });

        // Admin Functions
        function approveVideo(videoId) {
            if (confirm('Are you sure you want to approve this video?')) {
alert(`Video ${videoId} has been approved and is now live!`);
                // In a real application, this would update the database
                location.reload(); // Refresh to show updated status
            }
        }

        function rejectVideo(videoId) {
            if (confirm('Are you sure you want to reject this video?')) {
alert(`Video ${videoId} has been approved and is now live!`);
                // In a real application, this would update the database
                location.reload(); // Refresh to show updated status
            }
        }

        function deleteVideo(videoId) {
            if (confirm('Are you sure you want to delete this video? This action cannot be undone.')) {
alert(`Video ${videoId} has been approved and is now live!`);
                // In a real application, this would delete from the database
                location.reload(); // Refresh to show updated list
            }
        }

        // Update navigation for logged-in users
        function updateNavForLoggedInUser() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.innerHTML = `
    <a href="#" onclick="showPage('home')">Home</a>
    <a href="#" onclick="showPage('browse')">Browse</a>
    <a href="#" onclick="showPage('upload')">Upload</a>
    <a href="#" onclick="showUserDashboard()">Dashboard</a>
    <a href="#" onclick="logout()">Logout</a>
    <a href="#" onclick="showPage('admin')">Admin</a>
`;

        }

        function showUserDashboard() {
            alert('User dashboard would show uploaded videos, favorites, and account settings.');
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                alert('Logged out successfully!');
                location.reload(); // Reset to default state
            }
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial content
            showPage('home');
            
            // Add some sample notifications
            setTimeout(() => {
                console.log('StreamIt application loaded successfully!');
            }, 1000);
        });

        // Utility Functions
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // File upload preview
        document.getElementById('videoFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = formatFileSize(file.size);
                const maxSize = 100 * 1024 * 1024; // 100MB limit
                
                if (file.size > maxSize) {
                    alert('File size too large! Maximum allowed size is 100MB.');
                    this.value = '';
                    return;
                }
                
console.log(`Selected file: ${file.name} (${fileSize})`);
                
                // Auto-fill title from filename
                if (!document.getElementById('videoTitle').value) {
                    const filename = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                    document.getElementById('videoTitle').value = filename.replace(/[_-]/g, ' ');
                }
            }
        });

        // Search suggestions (mock data)
        const searchSuggestions = [
            'Getting Started',
            'Tutorial',
            'JavaScript',
            'CSS',
            'HTML',
            'React',
            'Node.js',
            'Database',
            'API',
            'Web Development'
        ];

        // Add search suggestions
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            if (query.length > 2) {
                const suggestions = searchSuggestions.filter(item => 
                    item.toLowerCase().includes(query)
                );
                
                // In a real application, you would show these suggestions in a dropdown
                console.log('Search suggestions:', suggestions);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close modals
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            }
            
            // Ctrl+/ for search
            if (e.ctrlKey && e.key === '/') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
            }
        });

        // Responsive navigation toggle (for mobile)
        function toggleMobileNav() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.style.display = navLinks.style.display === 'none' ? 'flex' : 'none';
        }

        // Add mobile navigation button for smaller screens
        if (window.innerWidth <= 768) {
            const navContainer = document.querySelector('.nav-container');
            const mobileToggle = document.createElement('button');
            mobileToggle.innerHTML = '☰';
            mobileToggle.style.cssText = `
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    display: block;
`;

            mobileToggle.onclick = toggleMobileNav;
            navContainer.appendChild(mobileToggle);
            
            // Hide nav links by default on mobile
            document.querySelector('.nav-links').style.display = 'none';
        }

        // Progress indicator for video uploads
        function showUploadProgress() {
            // This would be implemented with actual file upload
            const progressHTML = 
                <div style="margin-top: 1rem;">
                    <div style="background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                        <div style="background: linear-gradient(45deg, #667eea, #764ba2); height: 10px; width: 0%; transition: width 0.3s ease;" id="upload-progress"></div>
                    </div>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #666;">Uploading... 0%</p>
                </div>
            ;
            return progressHTML;
        }

function toggleReply(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        if (replyForm.style.display === 'none' || replyForm.style.display === '') {
            replyForm.style.display = 'block';
            document.getElementById(`reply-input-${commentId}`).focus();
        } else {
            replyForm.style.display = 'none';
            document.getElementById(`reply-input-${commentId}`).value = '';
        }
    }
}

// Toggle Like
function toggleLike() {
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }
    
    const likeBtn = document.getElementById('like-btn');
    likeBtn.disabled = true; // Prevent double-clicks
    
    fetch('ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_like&video_id=${videoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            document.getElementById('like-count').textContent = formatNumber(data.like_count);
            
            // Update button state
            if (data.liked) {
                likeBtn.classList.add('liked');
            } else {
                likeBtn.classList.remove('liked');
            }
            
            showNotification(data.message);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        likeBtn.disabled = false;
    });
}

// Post Comment or Reply
function postComment(parentId = null) {
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }
    
    const inputId = parentId ? `reply-input-${parentId}` : 'comment-input';
    const commentInput = document.getElementById(inputId);
    const comment = commentInput.value.trim();
    
    if (!comment) {
        showNotification('Please enter a comment', 'error');
        commentInput.focus();
        return;
    }
    
    if (comment.length > 1000) {
        showNotification('Comment is too long (max 1000 characters)', 'error');
        return;
    }
    
    // Disable input while posting
    commentInput.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('video_id', videoId);
    formData.append('comment', comment);
    if (parentId) formData.append('parent_id', parentId);
    
    fetch('ajax_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear input
            commentInput.value = '';
            
            // Update comment count
            document.getElementById('comment-count').textContent = formatNumber(data.comment_count);
            
            // Hide reply form if it was a reply
            if (parentId) {
                toggleReply(parentId);
            }
            
            // Show success message
            showNotification(data.message);
            
            // Reload page to show new comment
            // (You could also dynamically add the comment without reload)
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        commentInput.disabled = false;
    });
}

// Delete Comment
function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return;
    }
    
    fetch('ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete_comment&comment_id=${commentId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove comment from DOM
            const commentElement = document.getElementById(`comment-${commentId}`);
            if (commentElement) {
                // Fade out animation
                commentElement.style.opacity = '0';
                commentElement.style.transform = 'translateX(-20px)';
                commentElement.style.transition = 'all 0.3s';
                
                setTimeout(() => {
                    commentElement.remove();
                }, 300);
            }
            
            // Update comment count
            document.getElementById('comment-count').textContent = formatNumber(data.comment_count);
            
            showNotification(data.message);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Clear Comment Input
function clearComment() {
    const commentInput = document.getElementById('comment-input');
    if (commentInput) {
        commentInput.value = '';
        commentInput.blur();
    }
}

// Share Video
function shareVideo() {
    const shareData = {
        title: document.title,
        text: 'Check out this video on StreamIt!',
        url: window.location.href
    };
    
    // Try native share API (mobile)
    if (navigator.share) {
        navigator.share(shareData)
            .then(() => showNotification('Shared successfully!'))
            .catch(() => copyToClipboard(window.location.href));
    } else {
        // Fallback: Copy to clipboard
        copyToClipboard(window.location.href);
    }
}

// Copy to Clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
            .then(() => showNotification('Video link copied to clipboard!'))
            .catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }
}

// Fallback Copy Method
function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Video link copied to clipboard!');
    } catch (err) {
        showNotification('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textarea);
}

// Show Notification Toast
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification');
    existing.forEach(n => n.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Format Number (add commas)
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Auto-resize Textarea
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Add auto-resize to all textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('.comment-input');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    });
});

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to submit comment
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const activeElement = document.activeElement;
        
        if (activeElement && activeElement.classList.contains('comment-input')) {
            // Determine if it's a reply or main comment
            const parentId = activeElement.id.match(/reply-input-(\d+)/);
            if (parentId) {
                postComment(parseInt(parentId[1]));
            } else {
                postComment();
            }
        }
    }
});

// Character Counter
function updateCharCounter(textarea, counterId) {
    const counter = document.getElementById(counterId);
    if (counter) {
        const remaining = 1000 - textarea.value.length;
        counter.textContent = remaining;
        
        if (remaining < 100) {
            counter.style.color = '#e74c3c';
        } else {
            counter.style.color = '#888';
        }
    }
}

// Load More Comments (Optional - for pagination)
function loadMoreComments(offset = 10) {
    const formData = new FormData();
    formData.append('action', 'get_comments');
    formData.append('video_id', videoId);
    formData.append('limit', 10);
    formData.append('offset', offset);
    
    fetch('ajax_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.comments.length > 0) {
            // Append comments to list
            // You would need to implement the HTML generation here
            console.log('More comments loaded:', data.comments);
        } else {
            showNotification('No more comments to load');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load comments', 'error');
    });
}

// Prevent Double Submit
let commentSubmitting = false;

const originalPostComment = postComment;
postComment = function(...args) {
    if (commentSubmitting) {
        return;
    }
    commentSubmitting = true;
    
    originalPostComment.apply(this, args);
    
    setTimeout(() => {
        commentSubmitting = false;
    }, 2000);
};

console.log('StreamIt Like & Comment System Loaded ✓');
