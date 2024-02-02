Ext.define('SEMTI.view.informes.ResumendataFormComentFinal', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.ResumendataFormComentFinal',
    id: 'ResumendataFormComentFinal',
    margin: 0,
    padding: 0,
    layout: 'fit',
     
    items: [{
        xtype : 'textareafield',
        id : 'resumendataComentFinal',
        grow : true,
        name : 'comentario_final',
        border : false,
        anchor : '100%',
        disabled: true
    }]
});