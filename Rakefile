require 'httparty'
gem 'httparty', ">= 0.9.0"
require 'pp'
require 'open-uri'

desc 'Build docs'
task :build_docs do
  twitter = "markirby"
  name = "Chester WordPress MVC Theme Framework Documentation"
  theme = "v1"
  issues = true
  repo = "markirby/Chester-WordPress-MVC-Theme-Framework"
  
  file = URI::encode(IO.read("README.md"))
  vars = URI::encode("&twitter=#{twitter}&name=#{name}&theme=#{theme}&issues=#{issues}&repo=#{repo}")
  response = HTTParty.get("http://documentup.com/compiled?content=#{file}#{vars}")

  if (response.code == 200)
    File.open("index.html", 'w') {
      |f| f.write(response) 
    }
  end
  

end