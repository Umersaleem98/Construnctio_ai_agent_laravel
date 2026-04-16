import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', () => {
    const textareas = document.querySelectorAll('textarea[data-auto-resize]');
    
    textareas.forEach(textarea => {
        const resize = () => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        };
        
        textarea.addEventListener('input', resize);
        resize();
    });
});