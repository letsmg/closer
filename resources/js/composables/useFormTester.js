/**
 * Composable para auxiliar nos testes de formulários preenchendo-os automaticamente.
 */
export function useFormTester(form) {
  
  const fillConsumerForm = () => {
    const random = Math.floor(Math.random() * 1000);
    form.name = `Teste Usuário ${random}`;
    form.email = `teste${random}@closer.com`;
    form.password = 'Mudar@123';
    form.password_confirmation = 'Mudar@123';
    form.nickname = `nick${random}`;
    form.birth_date = '1995-05-07';
    form.gender = ['male', 'female', 'other'][Math.floor(Math.random() * 3)];
    form.gender_identity = 'Cisgênero';
    form.sexual_orientation = 'Heterossexual';
    form.purpose = ['serious', 'casual', 'friendship', 'all'][Math.floor(Math.random() * 4)];
  };

  const fillStaffForm = () => {
    const random = Math.floor(Math.random() * 1000);
    form.name = `Staff Admin ${random}`;
    form.email = `staff${random}@closer.com`;
    form.password = 'Mudar@123';
    form.nivel_acesso = [3, 4, 5][Math.floor(Math.random() * 3)];
  };

  const clearForm = () => {
    Object.keys(form).forEach(key => {
      if (typeof form[key] === 'string') {
        form[key] = '';
      } else if (typeof form[key] === 'number') {
        form[key] = key === 'nivel_acesso' ? 3 : 0;
      } else if (typeof form[key] === 'boolean') {
        form[key] = true;
      } else if (Array.isArray(form[key])) {
        form[key] = [];
      } else if (form[key] && typeof form[key] === 'object') {
        // Recursivo para objetos aninhados (como profile em UserEdit)
        Object.keys(form[key]).forEach(subKey => {
          form[key][subKey] = '';
        });
      }
    });
  };

  return {
    fillConsumerForm,
    fillStaffForm,
    clearForm
  };
}
