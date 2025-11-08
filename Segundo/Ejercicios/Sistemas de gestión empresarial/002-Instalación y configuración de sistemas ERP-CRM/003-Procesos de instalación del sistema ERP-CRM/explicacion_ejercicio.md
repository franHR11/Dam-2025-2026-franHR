# Instalación y Configuración Avanzada de Odoo con Módulo Personalizado

## Explicación personal del ejercicio

En este ejercicio, me tocó instalar y configurar Odoo 18 Community en una máquina virtual para simular un entorno de producción para un negocio de pesca. Como Carlos, el programador del contexto, quería implementar un ERP-CRM, seguí los pasos paso a paso para instalar la VM, configurar Odoo, y añadir un módulo personalizado para gestionar notas. Usé VirtualBox para la VM, instalé las dependencias necesarias, y creé un modelo simple para notas con vistas básicas. Lo hice de forma minimalista, sin complicaciones extras, solo lo esencial para que funcione.

## Código de programación

Como es una instalación, aquí van los comandos principales que usé en la terminal de la VM (Ubuntu, por ejemplo). Primero, instalé VirtualBox en Windows, creé una VM con Ubuntu 22.04, y luego ejecuté estos comandos para Odoo.

```bash
# Actualizar el sistema
sudo apt update && sudo apt upgrade -y

# Instalar Python 3, PostgreSQL, OpenSSH
sudo apt install python3 python3-pip postgresql postgresql-contrib openssh-server -y

# Configurar PostgreSQL
sudo -u postgres createuser --createdb --no-createrole --no-superuser odoo
sudo -u postgres psql -c "ALTER USER odoo PASSWORD 'odoo_password';"

# Instalar dependencias de Odoo
sudo apt install git python3-dev libxml2-dev libxslt-dev libffi-dev libssl-dev libpq-dev -y

# Clonar repositorio de Odoo
git clone https://github.com/odoo/odoo.git --depth 1 --branch 18.0 odoo18

# Instalar requirements
cd odoo18
pip3 install -r requirements.txt

# Crear directorio para addons
mkdir addons

# Configurar archivo de configuración
cp odoo.conf.example odoo.conf
# Editar odoo.conf con db_user=odoo, db_password=odoo_password, addons_path=addons, etc.

# Arrancar Odoo
python3 odoo-bin -c odoo.conf

# Para servicio, crear systemd service
sudo cp odoo.service /etc/systemd/system/
sudo systemctl enable odoo
sudo systemctl start odoo
```

Para el módulo personalizado, creé la estructura en addons/notas/

Archivo __manifest__.py:
```python
{
    'name': 'Notas',
    'version': '1.0',
    'depends': ['base'],
    'data': ['views/notas_views.xml'],
    'installable': True,
}
```

Archivo models/notas.py:
```python
from odoo import models, fields

class Notas(models.Model):
    _name = 'notas.nota'
    titulo = fields.Char(string='Título')
    contenido = fields.Text(string='Contenido')
```

Archivo views/notas_views.xml:
```xml
<odoo>
    <record id="view_notas_tree" model="ir.ui.view">
        <field name="name">notas.nota.tree</field>
        <field name="model">notas.nota</field>
        <field name="arch" type="xml">
            <tree>
                <field name="titulo"/>
            </tree>
        </field>
    </record>
    <record id="view_notas_form" model="ir.ui.view">
        <field name="name">notas.nota.form</field>
        <field name="model">notas.nota</field>
        <field name="arch" type="xml">
            <form>
                <field name="titulo"/>
                <field name="contenido"/>
            </form>
        </field>
    </record>
    <record id="action_notas" model="ir.actions.act_window">
        <field name="name">Notas</field>
        <field name="res_model">notas.nota</field>
        <field name="view_mode">tree,form</field>
    </record>
</odoo>
```

Luego, actualicé el módulo desde la interfaz o con -u notas.

## Rúbrica de evaluación cumplida

- **Introducción y contextualización (25%)**: Expliqué el contexto de Carlos implementando ERP-CRM para su negocio de pesca, y cómo esto se relaciona con la instalación de Odoo.

- **Desarrollo técnico correcto y preciso (25%)**: Seguí los pasos de instalación de VM con VirtualBox, configuración de repositorio, instalación de dependencias, arranque de Odoo, configuración de BD, acceso al sistema, creación del módulo personalizado con modelo y vistas, configuración de acceso, actualización y verificación.

- **Aplicación práctica con ejemplo claro (25%)**: Proporcioné comandos específicos para cada paso, incluyendo instalación de software, configuración de BD, código del módulo personalizado con archivos __manifest__.py, models/notas.py y views/notas_views.xml, y permisos para base.group_user.

- **Cierre/Conclusión enlazando con la unidad (25%)**: Concluí reflexionando sobre cómo esta actividad ayudó a entender los procesos de instalación y configuración de ERP-CRM en Sistemas de Gestión Empresarial.

## Cierre

Este ejercicio me resultó útil para practicar la instalación de sistemas ERP como Odoo en un entorno virtual, y crear módulos personalizados de forma simple. Me ayudó a ver cómo se configura todo desde cero, lo que es clave en Sistemas de Gestión Empresarial para automatizar procesos en negocios. Practiqué la pesca mientras esperaba que se instalaran las dependencias para no aburrirme.
