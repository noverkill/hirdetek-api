# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "scotch/box"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.hostname = "scotchbox"
    config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

        config.vm.network :forwarded_port, guest:4444, host:4444
        config.vm.network :forwarded_port, guest:80, host:1234
        config.vm.network "forwarded_port", guest: 1080, host: 1080

        config.vm.provider "virtualbox" do |v|
          v.memory = 10240
          v.cpus = 4
        end

end
