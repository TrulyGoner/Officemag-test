// Глобальный массив всех кнопок
let allButtons = [];
let currentPressedButton = null;

document.addEventListener('DOMContentLoaded', () => {
    allButtons = Array.from(document.querySelectorAll('.beautiful-button'));
    
    allButtons.forEach(button => {
        initButton(button);
    });
});

/**
 * @param {HTMLElement} 
 */
function initButton(button) {
    let isPressed = false;
    
    button.addEventListener('mousedown', (e) => {
        handlePress(button, e);
        isPressed = true;
    });

    button.addEventListener('mouseup', () => {
        handleRelease(button);
        isPressed = false;
    });

    button.addEventListener('mouseleave', () => {
        if (isPressed) {
            handleRelease(button);
            isPressed = false;
        }
    });

    button.addEventListener('touchstart', (e) => {
        handlePress(button, e.touches[0]);
        isPressed = true;
    });
    
    button.addEventListener('touchend', () => {
        handleRelease(button);
        isPressed = false;
    });

    button.addEventListener('click', (e) => {
        createRipple(button, e);
        handleButtonClick(button);
    });
}

/**
 * @param {HTMLElement} 
 */
function releaseAllButtonsExcept(exceptButton) {
    allButtons.forEach(button => {
        if (button !== exceptButton) {
            button.classList.remove('pressed');
        }
    });
}

/**)
 * @param {HTMLElement} 
 * @param {Event}
 */
function handlePress(button, e) {
    releaseAllButtonsExcept(button);

    button.classList.add('pressed');
    currentPressedButton = button;

    button.style.animation = 'none';
    setTimeout(() => {
        button.style.animation = '';
    }, 10);
}

/**
 * @param {HTMLElement} 
 */
function handleRelease(button) {
    // Не убираем класс 'pressed' при отпускании мыши
    // Кнопка останется в состоянии нажатой
}

/**
 * @param {HTMLElement} 
 * @param {Event} 
 */
function createRipple(button, e) {
    const ripple = button.querySelector('.ripple');

    const rect = button.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    ripple.style.left = `${x}px`;
    ripple.style.top = `${y}px`;
    ripple.style.width = '10px';
    ripple.style.height = '10px';

    ripple.classList.remove('animate');

    setTimeout(() => {
        ripple.classList.add('animate');
    }, 10);

    setTimeout(() => {
        ripple.classList.remove('animate');
    }, 600);
}

/**
 * @param {HTMLElement}
 */
function handleButtonClick(button) {
    const buttonNumber = button.getAttribute('data-button');

    console.log(`Кнопка ${buttonNumber} нажата`);

    if ('vibrate' in navigator) {
        navigator.vibrate(10);
    }
}

/**
 * @param {HTMLElement} 
 */
function addBounceEffect(button) {
    button.style.animation = 'bounce 0.3s ease';
    setTimeout(() => {
        button.style.animation = '';
    }, 300);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-5px) scale(1.02); }
    }
`;
document.head.appendChild(style);

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initButton,
        createRipple,
        handleButtonClick
    };
}
