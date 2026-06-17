// Inisialisasi variabel global chart agar aman jika dipanggil fungsi eksternal
let petugasChart = null;
let progressChart = null;

document.addEventListener("livewire:init", () => {
    // Dengarkan event update dari Livewire Backend tanpa mengunci render halaman jika Chart.js belum termuat
    Livewire.on("updateChart", (event) => {
        const chartData = event.data || event[0];

        if (!chartData) return;

        // JARING PENGAMAN: Panggil fungsi render grafik HANYA JIKA fungsi tersebut benar-benar ada di script view Anda
        if (typeof renderPetugasChart === "function") {
            renderPetugasChart(chartData);
        } else {
            console.warn(
                "Fungsi renderPetugasChart belum dimuat, data tabel tetap berhasil diperbarui.",
            );
        }

        if (typeof renderProgressChart === "function") {
            renderProgressChart(chartData);
        }
    });
});
