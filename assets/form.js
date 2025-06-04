
window.boulderForm = function () {
  return {
    boulderId: '',
    zone: false,
    top: false,
    attempts: 1,
    message: '',
    async submit() {
      const response = await fetch('/wp-json/bsp/v1/submit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          boulder: this.boulderId,
          zone: this.zone,
          top: this.top,
          attempts: this.attempts
        })
      });
      const result = await response.json();
      this.message = result.success ? 'Успешно записано!' : 'Грешка при запис!';
    }
  };
};
