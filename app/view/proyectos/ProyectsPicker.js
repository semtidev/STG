Ext.define('SEMTI.view.proyectos.ProyectsPicker', {
    extend: 'Ext.form.field.Picker',
    xtype: 'proyectsPicker',
    id: 'proyectsPicker',
    //triggerCls: Ext.baseCSSPrefix + 'form-arrow-trigger',
    //allowBlank: false,
    editable: false,
    msgTarget: 'side',
    emptyText: 'Seleccione el Objeto o el Local que ser\xE1 defectado',
    config: {
        /**
         * @cfg {Ext.data.TreeStore} store
         * A tree store that the tree picker will be bound to
         */
        //store: Ext.create('SEMTI.store.Proyectspicker'),

        /**
         * @cfg {String} valueField
         * The field inside the model that will be used as the node's id.
         * Defaults to the default value of {@link Ext.tree.Panel}'s `valueField` configuration.
         */
        valueField: 'id',
        /**
         * @cfg {String} displayField
         * The field inside the model that will be used as the node's text.
         * Defaults to the default value of {@link Ext.tree.Panel}'s `displayField` configuration.
         */
        displayField: 'text',
        /**
         * @cfg {Array} columns
         * An optional array of columns for multi-column trees
         */
        columns: null
    },
    /**
     * @cfg {String} emptyText
     * The default text to place into an empty field.
     */
    //emptyText: 'Seleccione el objeto o la parte que se defectar\xE1...',

    /**
     * @cfg {Boolean} matchFieldWidth
     * Whether the picker dropdown's width should be explicitly set to match the width of the field. Defaults to false.
     */
    matchFieldWidth: false,
    /**
     * @cfg {Boolean} selectOnlyLeafNode
     */
    selectOnlyLeafNode: true,
    initComponent: function() {
        this.callParent(arguments);

        this.addEvents(
                /**
                 * @event select
                 * Fires when a tree node is selected
                 * @param {Ext.ux.TreePicker} picker        This tree picker
                 * @param {Ext.data.Model} record           The selected record
                 */
                'select'
                );
    },
    /**
     * Creates and returns the tree panel to be used as this field's picker.
     * @private
     */
    createPicker: function() {
        treePicker = Ext.create('Ext.tree.Panel', {
            id: 'sdform_objectspicker',
            hidden: true,
            floating: true,
            resizable: false,
            resizeHandles: 's e se',
            autoScroll: true,
            width: 350,
            minWidth: this.bodyEl.getWidth(),
            height: 300,
            maxHeight: 300,
            shadow: true,
            cls: 'semtitreepicker',
            columns: this.columns,
            rootVisible: this.rootVisible,
            store: Ext.create('SEMTI.store.Proyectspicker'),
            listeners: {
                scope: this,
                focus: Ext.bind(this.onFocus, this),
                itemclick: Ext.bind(this.onItemClick, this)/*,
                 beforerender: function(){
                 this.getStore().reload();
                 }*/
            }
        });

        return treePicker;
    },
    /**
     * @private
     */
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
        var array_id = record.get('id').split('.');
        if (!this.selectOnlyLeafNode) {
            if (record.isLeaf() && array_id[0] != 2 && array_id[0] != 1) {
                this.selectNode(record);
            }
        }
        else if (array_id[0] != 2 && array_id[0] != 1) {
            this.selectNode(record);
        }
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
        //this.setRawValue(id);  //text
        this.inputEl.dom.value = ruta;
        this.inputEl.dom.style = 'color: #000000; width: 100%';
    },
    /**
     * Sets the specified value into the field
     * @param {Mixed} value
     */
    setValue: function(value) {
        var me      = this,
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
