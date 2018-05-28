# cbc-plugin
Plugin for custom post type and user Profile links with WpgraphQL support
Prerequisite: 

We have to install WPgraphQL first .

https://wpgraphql.com/

Add this extention to chrome : https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij?hl=en


To view the support we have to pass use these codes :

http://yousite.com/wpgraph/graphql?query={CaseStudies{edges{node{id,title,date,link}}}}


We can also use chrome extension 



{
  CaseStudies {
    edges {
      node {
        id
        title
        date
        link
      }
    }
  }
}


{
  user( id: "dXNlcjox") {
    id 
    username
      socialLinks {
          twitter
          facebook
          instagram
      }
  }
}


