// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import Lazyload from 'vanilla-lazyload'; 
// Create Lazy load instance
var lazyLoadInstance = new Lazyload({
    elements_selector: '.lazy'
    // ... more custom settings?
});
window.lazyLoadInstance = lazyLoadInstance;

console.log('Hello World 😎');

// Example of Babel.js transpiling
// Babel Input: ES2015 arrow function
[1, 2, 3].map((n) => n + 1);