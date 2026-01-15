// Плавная прокрутка при перетаскивании скроллбара
document.addEventListener('DOMContentLoaded', function() {
    // Принудительное скрытие стрелок скроллбара через инъекцию стилей
    const style = document.createElement('style');
    style.textContent = `
        .slider-wrapper::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            -webkit-appearance: none !important;
            appearance: none !important;
        }
        .slider-wrapper::-webkit-scrollbar-button:start:decrement,
        .slider-wrapper::-webkit-scrollbar-button:end:increment,
        .slider-wrapper::-webkit-scrollbar-button:horizontal:start:decrement,
        .slider-wrapper::-webkit-scrollbar-button:horizontal:end:increment,
        .slider-wrapper::-webkit-scrollbar-button:single-button:start:decrement,
        .slider-wrapper::-webkit-scrollbar-button:single-button:end:increment {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    `;
    document.head.appendChild(style);
    
    const sliderWrapper = document.querySelector('.slider-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    // Поддержка перетаскивания мышью
    sliderWrapper.addEventListener('mousedown', (e) => {
        isDown = true;
        sliderWrapper.style.cursor = 'grabbing';
        startX = e.pageX - sliderWrapper.offsetLeft;
        scrollLeft = sliderWrapper.scrollLeft;
    });

    sliderWrapper.addEventListener('mouseleave', () => {
        isDown = false;
        sliderWrapper.style.cursor = 'grab';
    });

    sliderWrapper.addEventListener('mouseup', () => {
        isDown = false;
        sliderWrapper.style.cursor = 'grab';
    });

    sliderWrapper.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - sliderWrapper.offsetLeft;
        const walk = (x - startX) * 2;
        sliderWrapper.scrollLeft = scrollLeft - walk;
    });

    // Добавляем курсор grab при наведении
    sliderWrapper.style.cursor = 'grab';
});
