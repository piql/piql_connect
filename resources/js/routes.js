import VueRouter from 'vue-router';

const landingPage = "stats.dashboard";
const views = require.context('./views/', true, /\.vue$/i);

/* Test for plural - only supports plural suffixes in list below */
function isPlural( word ) {
    const suffixes = ["es", "ts", "as", "ps" ];
    return !( suffixes.every( s => !word.endsWith( s ) ) );
}

/* Naive singularize - works when plural ending is one letter  */
function singularize( word ) {
    const suffixes = ["es", "ts", "as", "ps" ];
    if( isPlural (word ) )
        return word.slice( 0, -1 );
    return word;
}

const routes = views.keys()
    .map( fp => {
        const filePath = fp.toLowerCase();
        const np = filePath.split( ".vue" )[0]
            .split( "./" )
            .pop()
            .replace( /\//g, "." )
            .split(".");
        const name = np
            .map( (p,i) => np[i-1]
                ? p.startsWith(singularize(np[i-1])) ? p.split(singularize(np[i-1])).pop() : p
                : p
            )
            .join(".");

        const component = window.Vue.component( name, views(fp).default );

        let npp = filePath
            .replace( ".", "" ).split( ".vue" )[0]
            .split("/")
            .map( pp => isPlural(pp) ? pp.concat(`/:${singularize(pp)}Id`) : pp );


        let path = ( name == landingPage ) ? "/" :
            npp.map( (p,i) => npp[i-1]
                    ? p.startsWith(singularize(npp[i - 1].split("/")[0]))
                      ? p.split(singularize(npp[i-1].split("/")[0])).join("") : p
                    : p
            )
            .join("/");

        /* Check for index - index pages have the same name as the folder they are in, and no trailing route parameters */
        const parts = filePath.split(".vue")[0].split("/");
        if( parts[parts.length-1] == parts[parts.length-2] && path.split("/").length >2  ) {
            let pparts = path.split("/");
            let indexPath = ( pparts.length > 4 )
                ? pparts.slice(0, pparts.length-3).join("/")
                : "/"+parts.slice(1, parts.length-1 ).join("/");

            return {
                name: parts.slice(1,parts.length-1).join("."),
                path : indexPath,
                component,
                filepath: fp
            }
        }


        return {
            name,
            path,
            component,
            filepath: fp
        };
    });

const router = new VueRouter({
    mode: 'history',
    routes
});

export { router };
