using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Text.Json.Serialization;
using System.Threading.Tasks;

namespace PresenceApps
{
    public class Presence
    {
        public int ID { get; set; }

        [JsonPropertyName("NIS")]
        public string NIS { get; set; }

        [JsonPropertyName("Nama Lengkap")]
        public string NamaLengkap { get; set; }

        [JsonPropertyName("Kelas")]
        public string Kelas { get; set; }

        [JsonPropertyName("Waktu Masuk")]
        public string WaktuMasuk { get; set; }

        [JsonPropertyName("Status Masuk")]
        public string StatusMasuk { get; set; }

        [JsonPropertyName("Waktu Keluar")]
        public string WaktuKeluar { get; set; }

        [JsonPropertyName("Status Keluar")]
        public string StatusKeluar { get; set; }

        [JsonPropertyName("Alasan Datang")]
        public string AlasanDatang { get; set; }

        [JsonPropertyName("Alasan Pulang")]
        public string AlasanPulang { get; set; }
    }
}
