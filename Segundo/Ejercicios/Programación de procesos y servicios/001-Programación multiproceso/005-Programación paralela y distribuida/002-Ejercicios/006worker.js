// Worker que realiza un cálculo intensivo
self.onmessage = function(event) {
    const { taskId, numbers } = event.data;
    
    // Simular cálculo intensivo (multiplicación de números)
    let result = 1;
    for (let i = 0; i < numbers.length; i++) {
        result *= numbers[i];
    }
    
    // Enviar resultado al hilo principal
    self.postMessage({
        taskId: taskId,
        result: result
    });
};
