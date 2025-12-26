export const Validator = {
    email(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    phone(phone) {
        return /^[6-9]\d{9}$/.test(phone.replace(/\s+/g, ''));
    },

    password(password, min = 6) {
        return password.length >= min &&
               /[A-Za-z]/.test(password) &&
               /\d/.test(password);
    },

    name(name) {
        return /^[A-Za-z ]{3,100}$/.test(name.trim());
    },

    range(value, min, max) {
        if (isNaN(value)) return false;
        return value >= min && value <= max;
    },

    required(value) {
        return value !== null && value.trim() !== '';
    }
};
