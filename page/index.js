const API = window.env.APP_URL;

// Global variables - exactly matching C# Form1 class variables
let cardUID = ""; // Changed from StringBuilder to string like C# StringBuilder behavior
let totalSiswa = 0; // static Int64 in C#
let isQrShow = false; // bool in C#
let positionPresence = 0; // static int in C# - 0 = masuk, 1 = keluar
const DEFAULT_PHOTO = "/placeholder.svg?height=120&width=100";

// HttpClient equivalent
const httpClient = {
    async get(url) {
        const response = await fetch(url);
        return {
            isSuccessStatusCode: response.ok,
            statusCode: response.status,
            async readAsStringAsync() {
                return await response.text();
            }
        };
    },
    async post(url, content) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: content
        });
        return {
            async readAsStringAsync() {
                return await response.text();
            }
        };
    }
};

// Timer1_Tick equivalent - Indonesia timezone handling
function updateDateTime() {
    // SE Asia Standard Time equivalent
    const now = new Date();
    const indonesiaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
    
    const timeString = indonesiaTime.toLocaleString('id-ID', { 
        day: '2-digit',
        month: 'long', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    
    document.getElementById("timeDisplay").textContent = indonesiaTime.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit', 
        second: '2-digit',
        hour12: false
    });
    document.getElementById("dateDisplay").textContent = indonesiaTime.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Start the time update timer - exactly like C# timer1
setInterval(updateDateTime, 1000);
updateDateTime();

// getDataAll method - exact C# equivalent
async function getDataAll() {
    try {
        const response = await httpClient.get(API + "api/totalUsers");
        
        if (response.isSuccessStatusCode) {
            const responseString = await response.readAsStringAsync();
            const jsonResponse = JSON.parse(responseString);
            
            if (jsonResponse.hasOwnProperty("total") && jsonResponse.total != null) {
                document.getElementById("totalOverall").textContent = jsonResponse.total.toString();
            } else {
                document.getElementById("totalOverall").textContent = "0";
            }
        } else {
            document.getElementById("totalOverall").textContent = "0";
        }
    } catch (error) {
        if (error.name === 'TypeError') {
            showMessage("Gagal menghubungi server: " + error.message);
        } else if (error.name === 'SyntaxError') {
            showMessage("Format JSON tidak valid: " + error.message);
        } else {
            showMessage("Terjadi kesalahan: " + error.message);
        }
        document.getElementById("totalOverall").textContent = "0";
    }
}

// LoadDataAsync method - exact C# equivalent
async function LoadDataAsync() {
    try {
        const apiUrl = `${API}api/allAbsensi`;
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error(`Gagal mengambil data: ${response.status}`);
        }

        const jsonResponse = await response.text();
        
        // Parse JSON response with error handling
        let apiResponse;
        try {
            apiResponse = JSON.parse(jsonResponse);
        } catch (jsonError) {
            throw new Error("Format JSON tidak valid: " + jsonError.message);
        }

        if (!apiResponse || !apiResponse.data) {
            console.warn("Data tidak tersedia");
            return;
        }

        // Update statistics - matching C# LoadDataAsync logic
        document.getElementById("totalToday").textContent = apiResponse.count?.toString() || "0";
        document.getElementById("totalSickPermission").textContent = apiResponse.tth?.toString() || "0";
        totalSiswa = apiResponse.count || 0;

        // Update table - matching C# DataGridView update
        const tableBody = document.getElementById("attendanceTable");
        tableBody.innerHTML = "";

        apiResponse.data.forEach((row, index) => {
            const tr = document.createElement("tr");
            tr.className = "hover:bg-muted/50 transition-colors";

            // Map the columns according to C# code structure
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm">${index + 1}</td>
                <td class="px-6 py-4 text-sm font-medium">${row["NIS"] || "-"}</td>
                <td class="px-6 py-4 text-sm">${row["Nama Lengkap"] || "-"}</td>
                <td class="px-6 py-4 text-sm">${row["Kelas"] || "-"}</td>
                <td class="px-6 py-4 text-sm">${row["Waktu Masuk"] || "-"}</td>
                <td class="px-6 py-4 text-sm">${row["Waktu Keluar"] || "-"}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ${
                        row["Status Masuk"] === "Hadir"
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                    }">
                        ${row["Status Masuk"] || "-"}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ${
                        row["Status Keluar"] === "Hadir"
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                    }">
                        ${row["Status Keluar"] || "-"}
                    </span>
                </td>
            `;
            tableBody.appendChild(tr);
        });

    } catch (error) {
        console.error("Error dalam loadAbsensi:", error.message);
        
        // Error handling matching C# LoadDataAsync
        if (error instanceof TypeError && error.message.includes("fetch")) {
            showMessage("Gagal menghubungi server: " + error.message);
        } else if (error.message.includes("JSON")) {
            showMessage("Format JSON tidak valid: " + error.message);
        } else {
            showMessage("Terjadi kesalahan: " + error.message);
        }
    }
}

// ShowQRCodeDialog - exact C# equivalent
function ShowQRCodeDialog(url) {
    try {
        let countdown = 10;
        isQrShow = true;

        // container overlay
        const qrForm = document.createElement("div");
        qrForm.className =
            "fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50";

        qrForm.innerHTML = `
            <div class="relative bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full text-center space-y-4">
                <!-- Tombol close -->
                <button id="qrCloseBtn" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <h3 class="text-lg font-bold text-gray-800">Scan QR Code</h3>
                <div class="flex justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(
                        url
                    )}" 
                        alt="QR Code" class="w-72 h-72 border">
                </div>
                <p id="qrLabel" class="text-sm text-gray-600">
                    Scan QR code untuk melengkapi absensi Anda<br>
                    Jendela ini akan tertutup dalam 10 detik
                </p>
            </div>
        `;
        document.body.appendChild(qrForm);

        const label = qrForm.querySelector("#qrLabel");
        const closeBtn = qrForm.querySelector("#qrCloseBtn");

        // fungsi tutup dialog
        const closeDialog = () => {
            isQrShow = false;
            clearInterval(timer);
            qrForm.remove();
        };

        // klik tombol close
        closeBtn.addEventListener("click", closeDialog);

        // timer countdown
        const timer = setInterval(() => {
            countdown--;
            label.innerHTML = `Scan QR code untuk melengkapi absensi Anda<br>Tutup dalam ${countdown} detik...`;

            if (countdown <= 0) {
                closeDialog();
            }
        }, 1000);
    } catch (error) {
        showMessage("Error saat membuat QR Code: " + error.message);
    }
}

// SendCardUID - exact C# equivalent with proper error handling
async function SendCardUID(uid) {
    try {
        const data = {
            id: uid,
            mode: positionPresence == 0 ? "masuk" : "keluar"
        };
        const json = JSON.stringify(data);
        
        const response = await httpClient.post(API + "api/absensi", json);
        const responseString = await response.readAsStringAsync();
        
        const jsonResponse = JSON.parse(responseString);
        const status = jsonResponse.status || null;

        if (status === "success") {
            await LoadDataAsync();

            const warga = jsonResponse.data && jsonResponse.data.warga_tels ? jsonResponse.data.warga_tels : null;
            console.log("Warga Data:", warga);
            if (warga) {
                document.getElementById("studentName").textContent = warga.name || "-";
                document.getElementById("studentClass").textContent = warga.kelas || "-";
            } else {
                document.getElementById("studentName").textContent = "-";
                document.getElementById("studentClass").textContent = "-";
            }

            document.getElementById("studentNIS").textContent = (jsonResponse.data && jsonResponse.data.nis) ? jsonResponse.data.nis : "-";

            const imageUrl = jsonResponse.profile || null;
            if (imageUrl) {
                await LoadImageFromUrl(imageUrl);
            } else {
                document.getElementById("studentPhoto").src = DEFAULT_PHOTO;
            }

            // Panel visibility - exact C# logic
            document.getElementById("nfcScanning").classList.add("hidden"); // roundedPanel5.Visible = false
            document.getElementById("studentInfo").classList.remove("hidden"); // roundedPanel1.Visible = true
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                document.getElementById("studentInfo").classList.add("hidden");
                document.getElementById("nfcScanning").classList.remove("hidden");
            }, 5000);
            showMessage(jsonResponse.message || "Absensi berhasil.", "#28a745");
        } else if (status === "failed" || status === "libur") {
            const warga = jsonResponse.data && jsonResponse.data.warga_tels ? jsonResponse.data.warga_tels : null;
            console.log("Warga Data:", warga);
            if (warga) {
                document.getElementById("studentName").textContent = warga.name || "-";
                document.getElementById("studentClass").textContent = warga.kelas || "-";
            } else {
                document.getElementById("studentName").textContent = "-";
                document.getElementById("studentClass").textContent = "-";
            }

            document.getElementById("studentNIS").textContent = (jsonResponse.data && jsonResponse.data.nis) ? jsonResponse.data.nis : "-";

            const imageUrl = jsonResponse.profile || null;
            if (imageUrl) {
                await LoadImageFromUrl(imageUrl);
            } else {
                document.getElementById("studentPhoto").src = DEFAULT_PHOTO;
            }

            document.getElementById("nfcScanning").classList.add("hidden"); // roundedPanel5.Visible = false
            document.getElementById("studentInfo").classList.remove("hidden"); // roundedPanel1.Visible = true

            setTimeout(() => {
                document.getElementById("studentInfo").classList.add("hidden");
                document.getElementById("nfcScanning").classList.remove("hidden");
            }, 5000);
            
            await LoadDataAsync();
            showMessage(jsonResponse.message || "Terjadi kesalahan.");
        } else if (status === "late" || status === "early" || status === "non_productive") {
            if (jsonResponse.redirect_url) {
                const redirectUrl = jsonResponse.redirect_url;
                ShowQRCodeDialog(redirectUrl);
            }

            const warga = jsonResponse.data && jsonResponse.data.warga_tels ? jsonResponse.data.warga_tels : null;
            console.log("Warga Data:", warga);
            if (warga) {
                document.getElementById("studentName").textContent = warga.name || "-";
                document.getElementById("studentClass").textContent = warga.kelas || "-";
            } else {
                document.getElementById("studentName").textContent = "-";
                document.getElementById("studentClass").textContent = "-";
            }

            document.getElementById("studentNIS").textContent = (jsonResponse.data && jsonResponse.data.nis) ? jsonResponse.data.nis : "-";

            const imageUrl = jsonResponse.profile || null;
            if (imageUrl) {
                await LoadImageFromUrl(imageUrl);
            } else {
                document.getElementById("studentPhoto").src = DEFAULT_PHOTO;
            }

            document.getElementById("nfcScanning").classList.add("hidden"); // roundedPanel5.Visible = false
            document.getElementById("studentInfo").classList.remove("hidden"); // roundedPanel1.Visible = true

            setTimeout(() => {
                document.getElementById("studentInfo").classList.add("hidden");
                document.getElementById("nfcScanning").classList.remove("hidden");
            }, 5000);

            await LoadDataAsync();
        } else {
            showMessage(jsonResponse.message || "Status tidak dikenal: " + status);
        }
    } catch (error) {
        showMessage("Error: " + error.message);
    }
}

// Form_KeyPress
let isProcessing = false;
let resetTimer = null;

document.addEventListener("keypress", async (e) => {
    try {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();

            if (cardUID.length === 0 || isProcessing || isQrShow) return;

            isProcessing = true; // lock
            const hexUID = cardUID.trim();
            console.log("Detected Card UID: " + hexUID);

            try {
                await SendCardUID(hexUID); // tunggu sampai selesai
            } catch (err) {
                console.error("SendCardUID error:", err);
            } finally {
                cardUID = "";
                isProcessing = false;
                if (resetTimer) {
                    clearTimeout(resetTimer);
                    resetTimer = null;
                }
            }
        } else {
            cardUID += e.key;
            console.log("Current Card UID: " + cardUID);

            // ✅ auto reset setelah 200ms kalau tidak ada input baru
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(() => {
                cardUID = "";
                resetTimer = null;
                console.log("Card UID auto-reset");
            }, 200);
        }
    } catch (error) {
        isProcessing = false;
        showMessage("Terjadi kesalahan: " + error.message);
    }
});

function showMessage(msg, hexColor = "#f87171") {
    // overlay
    const overlay = document.createElement("div");
    overlay.style.position = "fixed";
    overlay.style.top = "0";
    overlay.style.left = "0";
    overlay.style.width = "100%";
    overlay.style.height = "100%";
    overlay.style.background = "rgba(0,0,0,0.1)"; // hitam transparan 10%
    overlay.style.zIndex = "9998";

    // box pesan
    const box = document.createElement("div");
    box.textContent = msg;
    box.style.position = "fixed";
    box.style.top = "50%";
    box.style.left = "50%";
    box.style.transform = "translate(-50%, -50%)";
    box.style.background = hexColor;
    box.style.color = "white";
    box.style.padding = "24px 40px";  // lebih besar
    box.style.fontWeight = "bold";
    box.style.boxShadow = "0 6px 12px rgba(0,0,0,0.3)";
    box.style.borderRadius = "12px";
    box.style.zIndex = "9999";
    box.style.fontSize = "18px"; // dibesarin
    box.style.textAlign = "center";

    document.body.appendChild(overlay);
    document.body.appendChild(box);

    setTimeout(() => {
        box.remove();
        overlay.remove();
    }, 1500); // auto hilang setelah 1 detik
}

// LoadImageFromUrl - exact C# equivalent
async function LoadImageFromUrl(url) {
    try {
        const img = document.getElementById("studentPhoto");
        
        // Test image loading like C# HttpClient approach
        const testImg = new Image();
        testImg.onload = function() {
            img.src = url;
        };
        testImg.onerror = function() {
            img.src = DEFAULT_PHOTO;
        };
        testImg.src = url;
        
    } catch (error) {
        document.getElementById("studentPhoto").src = DEFAULT_PHOTO;
    }
}

// button2_Click - exact C# equivalent
function button2_Click() {
    const button = document.getElementById("checkOutBtn");
    const buttonText = button.querySelector("span");
    const icon = button.querySelector("svg");
    const modeLabel = document.getElementById("attendanceMode");

    if (positionPresence == 0) {
        button.querySelector("span").textContent = "Absensi Masuk";
        positionPresence = 1;
        button.classList.remove("bg-primary", "hover:bg-primary/90");
        button.classList.add("bg-green-600", "hover:bg-green-700"); // Color.Green equivalent
        document.getElementById("attendanceMode").textContent = "Absensi Keluar";
    } else {
        button.querySelector("span").textContent = "Absensi Keluar";
        positionPresence = 0;
        button.classList.remove("bg-green-600", "hover:bg-green-700");
        button.classList.add("bg-primary", "hover:bg-primary/90"); // Color.Red equivalent
        document.getElementById("attendanceMode").textContent = "Absensi Masuk";
    }
}

// Form1_Load equivalent - DOMContentLoaded
document.addEventListener("DOMContentLoaded", async function() {
    // Initialize data loading - exact C# Form1_Load
    await getDataAll();
    await LoadDataAsync();
    
    // Initialize button click handler
    const checkOutBtn = document.getElementById("checkOutBtn");
    if (checkOutBtn) {
        checkOutBtn.addEventListener("click", button2_Click);
    }
    
    // Panel visibility initialization - exact C# constructor logic
    document.getElementById("nfcScanning").classList.remove("hidden"); // roundedPanel5.Visible = true
    document.getElementById("studentInfo").classList.add("hidden"); // roundedPanel1.Visible = false
    
    // Timer2 equivalent - refresh data every interval
    setInterval(LoadDataAsync, 10000); // timer2_Tick equivalent
});

// REMOVED ALL SIMULATION CODE - No more TEST_ prefix or fake UIDs
// The system now expects real NFC card UIDs from actual card readers