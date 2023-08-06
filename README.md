# Youtube API middleware
Middleware per la cache e l'adattamento delle richieste API verso youtube, specifiche rispetto alle playlist

- Il client fa una richiesta con l'id della playlist
- Il middleware controlla se nel DB vi è questa playlist
- Se no fa una chiamata alle API di YouTube, elimina i video privati o non in elenco, raggruppa le diverse "pagine" (ogni chiamata a YouTube contiene massimo 50 video) e lo inserisce nel DB
- Risponde alla chiamata con il JSON pulito della palylist richiesta e con la lista completa dei visto

Ho dovuto usare MySQL per la cache come soluzione temporanea, non appena possibile MaySQL sarà sostituito da Memcached.
Questo middleware è stato studiato per locarnofestival.ch
