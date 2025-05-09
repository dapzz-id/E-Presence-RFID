using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Text.Json.Serialization;
using System.Threading.Tasks;

namespace PresenceApps
{
    public class ApiResponse
    {
        [JsonPropertyName("data")]
        public List<Presence> Data { get; set; }

        [JsonPropertyName("count")]
        public int Count { get; set; }

        [JsonPropertyName("total_tidak_hadir")]
        public int TTH { get; set; }
    }
}
