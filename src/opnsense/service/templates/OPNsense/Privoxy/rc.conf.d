{% if helpers.exists('OPNsense.privoxy.general.enabled') and OPNsense.privoxy.general.enabled|default("0") == "1" %}
privoxy_var_script="/usr/local/opnsense/scripts/OPNsense/Privoxy/setup.sh"
privoxy_config="/usr/local/etc/privoxy/config"
privoxy_enable="YES"
{% else %}
privoxy_enable="NO"
{% endif %}
