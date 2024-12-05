
// Form Validation Functions
const validateLoginForm = () => {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!isValidEmail(email)) {
        showError('Please enter a valid email address');
        return false;
    }
    
    if (password.length < 8) {
        showError('Password must be at least 8 characters long');
        return false;
    }
    
    return true;
};

const validateRegisterForm = () => {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (username.length < 3) {
        showError('Username must be at least 3 characters long');
        return false;
    }
    
    if (!isValidEmail(email)) {
        showError('Please enter a valid email address');
        return false;
    }
    
    if (password.length < 8) {
        showError('Password must be at least 8 characters long');
        return false;
    }
    
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return false;
    }
    
    return true;
};

const validatePostForm = () => {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    const category = document.getElementById('category').value;
    
    if (title.length < 5) {
        showError('Title must be at least 5 characters long');
        return false;
    }
    
    if (content.length < 20) {
        showError('Content must be at least 20 characters long');
        return false;
    }
    
    if (!category) {
        showError('Please select a category');
        return false;
    }
    
    return true;
};

const validateCommentForm = () => {
    const content = document.getElementById('content').value.trim();
    
    if (content.length < 2) {
        showError('Comment must be at least 2 characters long');
        return false;
    }
    
    if (content.length > 10000) {
        showError('Comment is too long (maximum 10000 characters)');
        return false;
    }
    
    return true;
};

const validateProfileForm = () => {
    const email = document.getElementById('email').value;
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    
    if (!isValidEmail(email)) {
        showError('Please enter a valid email address');
        return false;
    }
    
    if (!currentPassword) {
        showError('Current password is required');
        return false;
    }
    
    if (newPassword && newPassword.length < 8) {
        showError('New password must be at least 8 characters long');
        return false;
    }
    
    return true;
};

const isValidEmail = (email) => {
    const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    return emailRegex.test(email);
};

const showError = (message) => {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    } else {
        alert(message);
    }
};

const initializeExpandingTextareas = () => {
    document.querySelectorAll('textarea[data-expanding]').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
};

const confirmDelete = (type) => {
    return confirm(`Are you sure you want to delete this ${type}?`);
};

const initializePreview = () => {
    const previewButton = document.getElementById('preview-button');
    const previewArea = document.getElementById('preview-area');
    const content = document.getElementById('content');
    
    if (previewButton && previewArea && content) {
        previewButton.addEventListener('click', () => {
            previewArea.innerHTML = markdownToHtml(content.value);
            previewArea.style.display = 'block';
        });
    }
};

const markdownToHtml = (text) => {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
};

const initializeNavigation = () => {
    const menuButton = document.getElementById('menu-toggle');
    const nav = document.querySelector('nav');
    
    if (menuButton && nav) {
        menuButton.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    }
};

const initializeInfiniteScroll = () => {
    let loading = false;
    const contentContainer = document.querySelector('.infinite-scroll-container');
    
    if (contentContainer) {
        window.addEventListener('scroll', () => {
            if (loading) return;
            
            const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
            
            if (scrollTop + clientHeight >= scrollHeight - 5) {
                loading = true;
                loadMoreContent()
                    .then(() => { loading = false; })
                    .catch(() => { loading = false; });
            }
        });
    }
};

const loadMoreContent = async () => {
    const container = document.querySelector('.infinite-scroll-container');
    const page = (parseInt(container.dataset.page) || 1) + 1;
    const url = `${window.location.pathname}?page=${page}`;
    
    try {
        const response = await fetch(url);
        const data = await response.text();
        container.insertAdjacentHTML('beforeend', data);
        container.dataset.page = page;
    } catch (error) {
        console.error('Error loading more content:', error);
    }
};

const initializeNotifications = () => {
    const notifications = document.querySelectorAll('.notification');
    
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initializeExpandingTextareas();
    initializePreview();
    initializeNavigation();
    initializeInfiniteScroll();
    initializeNotifications();
    
    if (!document.getElementById('error-message')) {
        const errorDiv = document.createElement('div');
        errorDiv.id = 'error-message';
        errorDiv.className = 'error-message';
        errorDiv.style.display = 'none';
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }
});

window.validateLoginForm = validateLoginForm;
window.validateRegisterForm = validateRegisterForm;
window.validatePostForm = validatePostForm;
window.validateCommentForm = validateCommentForm;
window.validateProfileForm = validateProfileForm;
window.confirmDelete = confirmDelete;