// Librería de componentes de interfaz de usuario personalizados
// Para interfaces empresariales

// Componente CustomSearchSelect: Select con búsqueda
class CustomSearchSelect {
  constructor(containerId, options) {
    this.container = document.getElementById(containerId);
    this.options = options;
    this.selectedValue = null;
    this.createElement();
  }

  createElement() {
    this.input = document.createElement('input');
    this.input.type = 'text';
    this.input.placeholder = 'Buscar...';
    this.input.className = 'custom-search-input';

    this.dropdown = document.createElement('ul');
    this.dropdown.className = 'custom-dropdown';
    this.dropdown.style.display = 'none';

    this.container.appendChild(this.input);
    this.container.appendChild(this.dropdown);

    this.input.addEventListener('focus', () => this.showDropdown());
    this.input.addEventListener('input', (e) => this.filterOptions(e.target.value));
    this.input.addEventListener('blur', () => setTimeout(() => this.hideDropdown(), 150));

    this.renderOptions(this.options);
  }

  renderOptions(options) {
    this.dropdown.innerHTML = '';
    options.forEach(option => {
      const li = document.createElement('li');
      li.textContent = option.label;
      li.dataset.value = option.value;
      li.addEventListener('click', () => this.selectOption(option));
      this.dropdown.appendChild(li);
    });
  }

  filterOptions(query) {
    const filtered = this.options.filter(opt => opt.label.toLowerCase().includes(query.toLowerCase()));
    this.renderOptions(filtered);
    this.showDropdown();
  }

  selectOption(option) {
    this.selectedValue = option.value;
    this.input.value = option.label;
    this.hideDropdown();
    // Disparar evento custom
    this.container.dispatchEvent(new CustomEvent('change', { detail: option }));
  }

  showDropdown() {
    this.dropdown.style.display = 'block';
  }

  hideDropdown() {
    this.dropdown.style.display = 'none';
  }
}

// Componente CurrencyInput: Input para monedas
class CurrencyInput {
  constructor(inputId) {
    this.input = document.getElementById(inputId);
    this.input.addEventListener('input', (e) => this.formatCurrency(e.target));
  }

  formatCurrency(target) {
    let value = target.value.replace(/[^\d.]/g, '');
    if (value) {
      value = parseFloat(value).toFixed(2);
      target.value = '€' + value;
    }
  }

  getValue() {
    return parseFloat(this.input.value.replace('€', ''));
  }
}

// Componente RatingStars: Estrellas para valoración
class RatingStars {
  constructor(containerId, maxStars = 5) {
    this.container = document.getElementById(containerId);
    this.maxStars = maxStars;
    this.rating = 0;
    this.createStars();
  }

  createStars() {
    for (let i = 1; i <= this.maxStars; i++) {
      const star = document.createElement('span');
      star.className = 'star';
      star.textContent = '☆';
      star.dataset.value = i;
      star.addEventListener('click', () => this.setRating(i));
      this.container.appendChild(star);
    }
  }

  setRating(rating) {
    this.rating = rating;
    const stars = this.container.querySelectorAll('.star');
    stars.forEach((star, index) => {
      star.textContent = index < rating ? '★' : '☆';
    });
    this.container.dispatchEvent(new CustomEvent('ratingChange', { detail: rating }));
  }

  getRating() {
    return this.rating;
  }
}

// Función para inicializar componentes
function initUIComponents() {
  // Los componentes se inicializan manualmente en el código de uso
}
