import './bootstrap';
import Swal from 'sweetalert2'
import 'flowbite';

window.Swal = Swal

// Now you can safely call it
initFlowbite();

document.addEventListener("livewire:navigating", () => {
    initFlowbite();
});

document.addEventListener("livewire:navigated", () => {
    initFlowbite();
});

