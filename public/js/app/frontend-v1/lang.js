define(['jed', 'json!/assets/frontend.trns.js?locale=' + window.appLocale], function(jed, response) {
    console.log('[app/lang]', 'Init');
    
    return new Jed({
        locale_data: response,
        domain: 'messages'
    });
});