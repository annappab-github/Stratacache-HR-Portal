<template>
  <nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <div class="navbar-brand logos">
          <img src="@/assets/Stratacachelogo.png" width="" height="" alt="airport logo">
          <img class="pl-3" src="../assets/scalalogo.png" width="30" height="20" alt="airport logo">
      </div>
    <div class="" id="">
      <ul class="nav">
        <li class="nav-item">
          <label class="nav-link active" href="#">HOME</label>
        </li>
        <li class="nav-item">
          <label class="nav-link" href="#">MY INFO</label>
        </li>
        <li class="nav-item">
          <label class="nav-link" href="#">PEOPLE</label>
        </li>
        <li class="nav-item">
          <label class="nav-link" href="#">REPORTS</label>
        </li>
        <li class="nav-item">
          <label class="nav-link" href="#">FILES</label>
        </li>
      </ul>
    </div>
  </div>
</nav>
</template>  

<script>

export default {
  name: 'Home',
  components: {
    
  }
};
</script>

<style>
li.nav-item{
  height: 40px;
}
.nav-link{
  color: #fff;
  font-size: 14px;
  font-family: 'Montserrat';
  letter-spacing: 0px;
}
.navbar {
background-image: url('../assets/navbar.png');
background-position: center;
background-repeat: no-repeat;
background-size: cover;
position: absolute;
height: 53px;
width: 792px;
left: 332px;
bottom: 0px;
}
.logos{
  color: #000;
  font-size: 25px;
  position: absolute;
  left: -300px;
  width: 100%;
  font-family: 'Montserrat';
  letter-spacing: 0px;
}
</style>
