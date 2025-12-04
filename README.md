# capejeer.com source code

This is the PHP source code for a site I created called [capejeer.com](https://capejeer.com/), which hosts movie reviews.
The site's implementation evolved a lot over the years, from simple static HTML to a homebrewed templating system to, eventually, PHP.
The main distinctive feature of the site's design is that it supports links between movies of the same series, allowing you to navigate sequentially through a given franchise's sequels, remakes, and so on.
It also supports indexing the film reviews by multiple criteria: title, release date, review score (which includes a finer-grained ranking than just the star ratings), and more.
It has a responsive layout usable on small devices, switching navigation from a sidebar to dropdowns.

I eventually decided to make the PHP and other code open-source so that others could make sites with similar features, and that's what's in this repo.
Note that it includes only the code (and a stylesheet) — not any of the site content displayed by the code.
If you run a local instance, you will get broken links for things like images.
For content, this repo includes a small sampling of fictional movies.
Note also that the code can reference a comments feature, but does not incorporate it.
You can license the commenting system used by Cape Jeer at [commentics.com](https://commentics.com/).

The license is that the source code included here is freely usable without restriction.
This permission does not extend to any content hosted by capejeer.com which is absent here.
All film reviews posted on capejeer.com retain full copyright by their author, Paul Kienitz.