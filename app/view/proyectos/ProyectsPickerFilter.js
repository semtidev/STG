Ext.define('SEMTI.view.proyectos.ProyectsPickerFilter', {
    extend: 'Ext.form.field.Picker',
    xtype: 'ProyectsPickerFilter',
    id: 'ProyectsPickerFilter',
    allowBlank: true,
    editable: true,
    msgTarget: 'side',
    emptyText: 'Todos los elementos de todos los Proyectos',
    config: {
        valueField: 'id',
        displayField: 'text',
        columns: null
    },
    matchFieldWidth: false,
    initComponent: function() {
        this.callParent(arguments);
        this.addEvents(
                'select'
                );
    },
    createPicker: function() {
        var me = this,
                treePicker = Ext.create('Ext.tree.Panel', {
                    hidden: true,
                    floating: true,
                    resizable: false,
                    resizeHandles: 's e se',
                    autoScroll: true,
                    width: 300,
                    minWidth: this.bodyEl.getWidth(),
                    height: 200,
                    maxHeight: 300,
                    shadow: true,
                    cls: 'semtitreepicker',
                    columns: this.columns,
                    rootVisible: this.rootVisible,
                    store: Ext.create('SEMTI.store.Proyectspicker'),
                    listeners: {
                        scope: this,
                        expand: Ext.bind(this.onFocus, this),
                        itemclick: Ext.bind(this.onItemClick, this)
                    }
                });

        return treePicker;
    },
    onFocus: function() {
        this.expand();
    },
    /**
     * Handles a click even on a tree node
     * @private
     * @param {Ext.tree.View} view
     * @param {Ext.data.Model} record
     * @param {HTMLElement} node
     * @param {Number} rowIndex
     * @param {Ext.EventObject} e
     */
    onItemClick: function(view, record, item, index, e, eOpts) {
        this.selectNode(record);
    },
    /**
     * Changes the selection to a given record and closes the picker
     * @private
     * @param {Ext.data.Model} record
     */
    selectNode: function(record) {
        this.setFieldValue(record.raw[this.valueField], record.get('ruta'));  ///record.raw[this.displayField]
        this.fireEvent('select', this, record.get(this.valueField));
        this.collapse();
    },
    /**
     * @private
     * @param {String} id
     * @param {String} text
     */
    setFieldValue: function(id, ruta) {
        this.setValue(id);
        this.inputEl.dom.value = ruta;
        this.inputEl.dom.style = 'color: #000000; width: 100%';
    },
    /**
     * Sets the specified value into the field
     * @param {Mixed} value
     */
    setValue: function(value) {
        var me = this,
                inputEl = me.inputEl;

        /*if (inputEl && me.emptyText && !Ext.isEmpty(value)) {
            inputEl.removeCls(me.emptyCls);
        }*/

        me.value = value;
        me.applyEmptyText();
    },
    /*setRawValue: function(value) {
     
     var me       = this,
     array_id = value.split('.');
     
     Ext.Ajax.request({ //dispara la petición
     
     url: './php/garantia/SdActions.php',
     method:'POST', 
     params:{accion: 'GetTextTreepicker', element: array_id[0], id_element: array_id[2]},
     success: function(result, request) { 
     
     var jsonData = Ext.JSON.decode(result.responseText);
     
     if(!jsonData.failure){
     
     me.inputEl.dom.value = jsonData.texto;
     }
     else{
     
     Ext.MessageBox.show({
     title: 'Mensaje del Sistema',
     msg: jsonData.message,
     buttons: Ext.MessageBox.OK,
     icon: Ext.MessageBox.ERROR
     });	
     }
     },
     failure: function() { 
     
     Ext.MessageBox.show({
     title: 'Mensaje del Sistema',
     msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
     buttons: Ext.MessageBox.OK,
     icon: Ext.MessageBox.ERROR
     });
     }
     });
     
     //this.inputEl.dom.value = value == null ? '' : value;
     },*/

    /**
     * Returns the current data value of the field (the idProperty of the record)
     * @return {Mixed} value
     */
    getValue: function() {
        return this.value;
    }

})
