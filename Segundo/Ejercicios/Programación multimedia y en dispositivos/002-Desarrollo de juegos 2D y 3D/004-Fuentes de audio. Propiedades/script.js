class Robot {
    constructor(x, y, angle) {
        this.x = x;
        this.y = y;
        this.angle = angle; // en radianes
    }

    update(speed, rotationSpeed) {
        this.angle += rotationSpeed;
        this.x += Math.cos(this.angle) * speed;
        this.y += Math.sin(this.angle) * speed;
    }

    checkSensors(obstacles) {
        // Simulación simplificada de un sensor frontal
        // Proyectamos un punto adelante
        let sensorX = this.x + Math.cos(this.angle) * 30;
        let sensorY = this.y + Math.sin(this.angle) * 30;

        for (let obs of obstacles) {
            if (sensorX > obs.x && sensorX < obs.x + obs.w &&
                sensorY > obs.y && sensorY < obs.y + obs.h) {
                return { x: sensorX, y: sensorY }; // Retorna punto de contacto
            }
        }
        return null; // Nada detectado
    }

    // Método extra solo para visualización
    draw(ctx) {
        ctx.save();
        ctx.translate(this.x, this.y);
        ctx.rotate(this.angle);
        ctx.fillStyle = 'blue';
        ctx.fillRect(-10, -10, 20, 20); // Cuerpo del robot
        ctx.fillStyle = 'red';
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(30, 0); // Línea de dirección/sensor
        ctx.stroke();
        ctx.restore();
    }
}
