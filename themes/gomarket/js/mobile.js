var mobileHelper = (function () {

    /**
     * Obtiene un array de strings de los diferentes devices y devuelve true o false si se encuentra en ese dispositivo
     * valores disponibles: android, iPad, iPhone, iPod, windows phone
     * @param kind_device array<String>
     * @author Santiago Moreno
     */
    function deviceVerification(kind_device = devicesList()) {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        var devices = "";
        for (var n_devices = 0; n_devices < kind_device.length; n_devices++) {
            if (typeof kind_device[n_devices] === "string" && devicesList().indexOf(kind_device[n_devices]) !== -1) {
                devices += (n_devices === 0 ? "" : "|") + kind_device[n_devices];
            } else {
                return { success: false, error: "el array no debe incluir números" };
            }
        }
        return (new RegExp(devices, "i").test(userAgent));
    }

    /** 
     * Obtiene el listado de dispositivos admitidos por el JS
     * @author Santiago Moreno
    */
    function devicesList() {
        var list = ["android", "iPad", "iPhone", "iPod", "windows phone"];
        return list;
    }

    return { deviceVerification: deviceVerification };
})();