import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';


import Swal from 'sweetalert2';
window.Swal = Swal;


// вместо Select2:
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.css";

// после того, как страница загрузится:
document.addEventListener('DOMContentLoaded', () => {
    new TomSelect('#languages-select', {
        plugins: ['remove_button'],
        placeholder: 'Выберите языки',
        maxItems: null,     // множественный выбор
        valueField: 'value',
        labelField: 'text',
        searchField: 'text',
        options: Array.from(document.querySelectorAll('#languages-select option')).map(opt => ({
            value: opt.value,
            text: opt.textContent
        }))
    });
});

import 'bootstrap-icons/font/bootstrap-icons.css';





import '../css/app.css';

// Импорт стилей для шапки и подвала
import '../css/components/header.css';
import '../css/components/footer.css';
