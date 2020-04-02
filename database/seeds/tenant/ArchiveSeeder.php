<?php

use Illuminate\Database\Seeder;
use App\Archive;
use \App\Traits\SeederOperations;

class ArchiveSeeder extends Seeder
{
    use SeederOperations;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Archive::truncate();

        if($this->seedFromFile(function($param) {
                Archive::create($param);
            })){

            return;
        }

        Archive::create([
            'title' => 'Forsvarsmuseet',
            'description' => 'Forsvarsmuseet er hovedmuseum i Forsvaret og har holdt til i en gammel arsenalbygning på Akershus festning siden 1860. Utstillingene viser historien til det norske Forsvaret, med vekt på tiden fra 1400-tallet til våre dager. Forsvarsmuseet skal formidle informasjon slik at man på historisk grunnlag skal kunne ta standpunkt i forsvarshistoriske spørsmål, og til Forsvarets rolle i dagens situasjon.',
            'uuid' => '9aae5540-d3ec-11e9-9a0b-ddd5a3958760'

        ]);

        Archive::create([
            'title' => 'Bergenhus',
            'description' => 'Det har vært arbeidet med å opprette et museum viet motstandskampen under den annen verdenskrig (1940-45) fra kort tid etter krigens slutt. Mange gode initiativ hadde ikke ført frem. Men da omstillingene i Forsvaret for alvor skjøt fart fra 1996, tok kommandanten på Bergenhus initiativ til at magasinbygningen på Koengen kunne frigjøres til museumsformål. Etter påtrykk fra Bergen Forsvarsforening, bevilget Stortinget midler fra 1999, som sammen med innsamlede penger, utvirket at Magasinbygningen kunne ombygges. Den 9 april 2006 kunne museet endelig åpnes av Gunnar Sønsteby. Tre utstillinger var da på plass, siden har flere kommet til.'
        ]);

        Archive::create([
            'title' => 'Luftforsvarsmuseet',
            'description' => 'Luftforsvarsmuseets primære utstilling i Bodø består av samlinger av over 25 fly, flymodeller, tablå av flyfabrikk, kontroll og varslingsanlegg samt mange større gjenstander som luftvernkanoner, missilsystemer og flere radar- og peilesystemer. I tillegg finnes det en mengde større og mindre gjenstander som forteller historien om militærflygningen fra de tidligste forsøk til dagens Luftforsvar. Utstillingen viser samlinger fra ulike tidsepoker og tematiske områder og forsøker å vise bakgrunnen og fremveksten av et luftforsvar i Norge.'
        ]);

        Archive::create([
            'title' => 'Hjemmefrontmuseet',
            'description' => 'Norges Hjemmefrontmuseum (NHM) er landets ledende institusjon for okkupasjonshistorie. Museet ønsker å formidle kunnskap om okkupasjonsårene og motstandskampen, samt skape et levende inntrykk av hvilken ulykke og fornedrelse en okkupasjon er for et folk. Museet holder til i bygningen "Det dobbelte batteri og bindingsverkshuset" på toppen av Akershus festning som stammer fra siste halvdel av 1600-tallet.'
        ]);

        Archive::create([
            'title' => 'Rustkammeret',
            'description' => 'Forsvarsmuseet Rustkammerets hovedoppgave er å bevare, dokumentere og formidle forsvarets historie i Midt-Norge. Museet er et av sju museum som tilhører Forsvarets museer (FM) enavdeling under Forsvarets Fellestjenester (FFT).'
        ]);

        Archive::create([
            'title' => 'Marinemuseet',
            'description' => 'Marinemuseet ligger i Horten, på Marinens gamle hovedbase Karljohansvern, som nå er erklært som et av Norges nasjonale festningsverk. Marinemuseet ble etablert i 1853 og siden 1864 har det holdt til i en av Marinens gamle magasinbygninger. Det er dermed et av de marinemuseer i verden som lengst har holdt sammenhengende åpent for publikum.'
        ]);

        Archive::create([
            'title' => 'Oscarsborg',
            'description' => 'Oscarsborg festning ligger på Kaholmene i Drøbaksundet, ca. 35 km syd for Oslo. Akkurat der er Oslofjorden på sitt smaleste og gir naturlige betingelser for forsvar mot angrep fra sjøsiden. I flere hundre år har festningsanlegg her voktet innseilingen til hovedstaden. Og spesielt på grunn av hendelsene 9. april 1940, står Oscarsborg i dag som et unikt symbol for Norges frihet og besøkes årlig av historisk interesserte mennesker fra alle verdenshjørner.'
        ]);
    }
}
