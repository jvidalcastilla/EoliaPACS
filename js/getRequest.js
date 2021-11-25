/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getRequest(url, success, error) {
                var req = false;
                try {
                    // most browsers
                    req = new XMLHttpRequest();
                } catch (e) {
                    // IE
                    try {
                        req = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        // try an older version
                        try {
                            req = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch (e) {
                            return false;
                        }
                    }
                }
                if (!req)
                    return false;
                if (typeof success != 'function')
                    success = function () {};
                if (typeof error != 'function')
                    error = function () {};
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        return req.status === 200 ?
                                success(req.responseText) : error(req.status);
                    }
                }
              //  alert (url);
                req.open("GET", url, true);
                req.send(null);
                return req;
            }