pimcore.registerNS("pimcore.plugin.TodoListBundle");

pimcore.plugin.TodoListBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.TodoListBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("TodoListBundle ready!");
    }
});

var TodoListBundlePlugin = new pimcore.plugin.TodoListBundle();
