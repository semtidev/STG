Ext.onReady(function(){
    
	Ext.tip.QuickTipManager.init();
	        	
	// Definir modificaciones en el idioma de funciones nativas
	Ext.define("Ext.locale.es.grid.Lockable", {
        override: "Ext.grid.Lockable",
        unlockText: "Desbloquear",
        lockText: "Bloquear"
    });
    
});


