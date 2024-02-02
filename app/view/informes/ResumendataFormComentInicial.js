Ext.define('SEMTI.view.informes.ResumendataFormComentInicial', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.ResumendataFormComentInicial',
    id: 'ResumendataFormComentInicial',
    margin: 0,
    padding: 0,
    layout: 'fit',
     
    items: [{
        xtype : 'textareafield',
        id : 'resumendataComentInicial',
        grow : true,
        name : 'comentario_inicial',
        border : false,
        anchor : '100%',
        disabled: true
    }]
});