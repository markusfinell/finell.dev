document.documentElement.style.setProperty('--root-size', window.getComputedStyle(document.documentElement).fontSize.replace('px', ''));

setTimeout(() => {
    document.querySelector('.logo').classList.add('transition-active');
}, 2000);