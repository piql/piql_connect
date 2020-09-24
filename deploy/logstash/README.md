The most recent "official" plugin for logstash-output-mongodb (3.1.6) has some serious flaws, and is missing an important option to upsert documents. A number of pull requests address these issues, but for some reason the plugin admins have not approved any PRs for about a year. We base our version of the plugin on a feature branch from contributor iteratec, where all we need is implemented.

We hope that this is a temporary solution and that a new official release will be available soon. Until then we need to stick to this rather hackish solution. If we for some reason need to recreate the gem, here are the instructions:

First checkout the repo:
```
git clone https://github.com/iteratec/logstash-output-mongodb.git
cd logstash-output-mongodb/
git checkout feature/implementDifferentActions
```

Then update some versions in `logstash-output-mongodb.gemspec`:
```
line 4:   s.licenses        = ['Apache-2.0']
line 23:  s.add_runtime_dependency 'logstash-codec-plain', '~> 3.0'
line 26:  s.add_development_dependency 'logstash-devutils', '~> 0'
```
(line 23 is the important one, as omitting it will cause the plugin loader to skip it)

Finally, build the gem:
```
gem build logstash-output-mongodb.gemspec
```

NOTE: If the version number of the plugin changes, you will need to update init.sh and the docker-compose.yml to copy and utilize the new plugin in the logstash docker.
