jQuery(function () {
    document.formvalidator.setHandler('name', value => /^.+$/.test(value));
    document.formvalidator.setHandler('value', value => /^.+$/.test(value));
})