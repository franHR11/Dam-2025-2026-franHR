// build: g++ -O2 -march=native -std=c++17 -o paralelo paralelo.cpp
// run  : ./paralelo

#include <iostream>
#include <vector>
#include <cstdlib>
#include <cstdint>
#include <ctime>
#include <unistd.h>
#include <sys/wait.h>
#include <sched.h>

static volatile double g_sink;  // evita que el compilador elimine el cálculo

int main() {
    constexpr int PROCESOS = 16;
    constexpr uint64_t ITERACIONES = 10'000'000'000ULL;
    constexpr double FACTOR = 1.0000000000654;
    constexpr double INICIAL = 1.00000098;

    // tiempo de inicio (segundos epoch), como en tu Python
    time_t inicio = std::time(nullptr);

    std::vector<pid_t> pids;
    pids.reserve(PROCESOS);

    int num_cores = sysconf(_SC_NPROCESSORS_ONLN);
    if (num_cores <= 0) num_cores = PROCESOS;

    for (int k = 0; k < PROCESOS; ++k) {
        pid_t pid = fork();
        if (pid < 0) {
            std::perror("fork");
            // si falla, esperamos a los que ya se lanzaron
            for (pid_t p : pids) waitpid(p, nullptr, 0);
            return 1;
        }
        if (pid == 0) {
            // Hijo: opcional, fijar afinidad a un core (Linux)
            cpu_set_t set;
            CPU_ZERO(&set);
            CPU_SET(k % num_cores, &set);
            (void)sched_setaffinity(0, sizeof(set), &set); // ignorar error si no permitido

            // Cálculo similar al original
            std::cout << "empiezo\n";
            double numero = INICIAL;
            for (uint64_t i = 0; i < ITERACIONES; ++i) {
                numero *= FACTOR;
            }
            g_sink = numero;  // prevenir eliminación por optimizador

            _exit(0);
        } else {
            // Padre
            pids.push_back(pid);
        }
    }

    // Esperar a que terminen todos los procesos
    for (pid_t p : pids) {
        waitpid(p, nullptr, 0);
    }

    time_t final = std::time(nullptr);
    std::cout << "he tardado " << (final - inicio) << " segundos\n";
    return 0;
}

