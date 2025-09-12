import json

agenda = [
        {
            "nombre":"Francisco Jose",
            "telefono":["612345678","623456789","634567890"],
            "email":"desarrollo@pcprogramacion.es",
            },
        {
            "nombre":"Juan",
            "telefono":["645678901","656789012","667890123"],
            "email":"contacto@mariacompany.com",
            },
        {
            "nombre":"Ana",
            "telefono":["678901234","689012345","690123456"],
            "email":"ventas@anacompany.com",
            },
        {
            "nombre":"Luis",
            "telefono":["690123456","689012345","678901234"],
            "email":"info@luiscompany.com",
            },
    ]

archivo = open("agenda.json",'w')
json.dump(agenda,archivo,indent=4)
archivo.close()
