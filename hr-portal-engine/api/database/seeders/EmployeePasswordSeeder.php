<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// use Spatie\Permission\Models\User;

class EmployeePasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $passwords = array(
            array('empid' => 'ADM01' , 'password' => '$2y$10$krfbrFRkFiqKuHk0LBY8S.KGXhWilPwr0eDXiuC3TfTH5mNPt8nL.'),
            array('empid' => 'HRMAUDIT01', 'password' => '$2y$10$IHVk.Vj1mIsmw/zm2XKpp.xGFKkQUH1p2xjKD3xAFCP/glxAWvz6C'),
            array('empid' => 'SG001', 'password' => '$2y$10$5TZJD4doxkYp9HMY4wEh0u2AGH8nCF7IyrWmhG.XCHe31Mo3R56kK'),
            array('empid' => 'SG002', 'password' => '$2y$10$a5mwqONH3QKA.U61gpTuTOH35t58xfstCx4XBgS4NoWeMRCRQzhUu'),
            array('empid' => 'SG003', 'password' => '$2y$10$eTa30sBFtNtIYsNABvZm8ufYLGXVCo.qPRr4LGFoCnJEPo9G7KmES'),
            array('empid' => 'SG004', 'password' => '$2y$10$u0jiXDrnrMHZWiwbPnXSFOYjvYM.5FaXE4ARWoP1jzIps6Av0oOA6'),
            array('empid' => 'SG005', 'password' => '$2y$10$Pb6PWAgseOUl6kWoh6vlWONUHTyEoAYPlSxL/EbHpxxvC46hxfXd2'),
            array('empid' => 'SG006', 'password' => '$2y$10$xSPuov1eNMdaRO7GWds9f.1qRKqsvbSbxfeTa9WZTSqCDhT6K4WaO'),
            array('empid' => 'SG008', 'password' => '$2y$10$vnVv8YfG5.Jik/KJJepHj.hj/2SLz/X.566bPQxW5PYxwkcTbXn0O'),
            array('empid' => 'SG009', 'password' => '$2y$10$YCXbBq/pnDrdDyPQu2xq4u6Kwtio5oDuLTCJZRXmP2qqIcmpMZ3S.'),
            array('empid' => 'SG010', 'password' => '$2y$10$XHGD3g5biMmRBCEjTCUbh.eUOIM2fl5JCOrUr4PqNI2BhHlnM6w1y'),
            array('empid' => 'SG011', 'password' => '$2y$10$IJWdTJ42LUBcJuar9qEEJ.gFmL1t4blszywIXtIHODHVZI05mf7eq'),
            array('empid' => 'SG012', 'password' => '$2y$10$96h8XiDObgqrFUb6XSRzY.c9mf/DBJYW.wHylqm4SaqVeSfVT3x8y'),
            array('empid' => 'SG013', 'password' => '$2y$10$BuTFXMCknuVJYVlCEAcpoee8122rWyZxLEhU1DyKO/Lwa/8m16FFy'),
            array('empid' => 'SG014', 'password' => '$2y$10$OQhfaVpWvgDcGinPXS34tuERYy8drTUbGl2K9IEEbQ1gTqvNrKAqO'),
            array('empid' => 'SG015', 'password' => '$2y$10$SIB8TyjQn2DYRO8tHWHDGOPAl2qxoOwO5PKpURnWurT8IT56fGlaO'),
            array('empid' => 'SG016', 'password' => '$2y$10$DtVMZfvHK4Qy79QGBUjYJO3RAXykco09QqLDsfNETkUitMJTZDTVe'),
            array('empid' => 'SG017', 'password' => '$2y$10$BDq2OWQGOQq75GKh/YL.SuOsQKRQZ3./YK.nuRP06btfXvUtEllCa'),
            array('empid' => 'SG018', 'password' => '$2y$10$WcBiKOcB/6Owj02EbsBDCOr.As3m7QU9A86yhUsfMJgviW0KYyZGq'),
            array('empid' => 'MY001', 'password' => '$2y$10$iF7kXEVUsDgJ90pxAgbWHO9BczuK3xC7coHAngizHc6NO3wBFzl4m'),
            array('empid' => 'AUS001', 'password' => '$2y$10$OKYjO71CCDq7dZOXn1uFPOSj4CvIAZIRMUcDdpx3iqMFDumDLuv/S'),
            array('empid' => 'AUS002', 'password' => '$2y$10$I20jSjazL84Gne56LFkemeF5RIQERleO4mN.Fn0A2PBSohHtr23HK'),
            array('empid' => 'AUS003', 'password' => '$2y$10$ZmOsl00ORQpTwIGhxDh6ueUJ13ZicF20r5eWhtd3UIAp6FF8QaP/u'),
            array('empid' => 'AUS005', 'password' => '$2y$10$xthmynozxv6Q9o9QqbHZ9uHvxC5LrlACXWQ27Zt5xykgh/ySLru3u'),
            array('empid' => 'AUS006', 'password' => '$2y$10$32BfNnuT65paxN0o1BbHd.9uVR7r8J5xBWjNIvXTW3yin3HJssOES'),
            array('empid' => 'AUS007', 'password' => '$2y$10$14c9l372bWFovNIdr46p3.XZyRLs6/5mnijHph8OLBTAEzr6EyWwu'),
            array('empid' => 'HK001', 'password' => '$2y$10$0iz8OAHAXltysRGbarLobuxlxkY6.KKddDIr7Lxwg/5GF7U0UBY8i'),
            array('empid' => 'HK002', 'password' => '$2y$10$w3TnCCtp..efFPSJ8FkDLeN9fPuiWPS9U.0iO99cpKzG7cv5cR7u2'),
            array('empid' => 'HK003', 'password' => '$2y$10$eI.1HX/SB1.nhHwyM/A2euxCnG20.vj1C3189BfE1wNFX224pLgQS'),
            array('empid' => 'CH002', 'password' => '$2y$10$5e1lcojxHyV/CwqnnOoSEuybrfnZ8z9wAfVwkloNhuwOPRXA1fdeO'),
            array('empid' => 'JP001', 'password' => '$2y$10$zcWptifl7mK6MnRWhRYtfeQvhDjWXXtvkAIJtsDfkXsiUKErh3NYq'),
            array('empid' => 'JP002', 'password' => '$2y$10$Dof5TxmN4qHxlJyLRZHfkOtz01FtqKJtc6xjND2V5tgHUc5/OBr8G'),
            array('empid' => 'JP003', 'password' => '$2y$10$V//01Jerj7b21p6Rasnmte4mSJmuKK6W8g93HwMMBBICCfO0qewoW'),
            array('empid' => 'JP005', 'password' => '$2y$10$zDyp.V7SQQSnKjZY.Z6B4.I/GRBeLE3lnoCfgCXYk6MorJZARPLXC'),
            array('empid' => 'JP006', 'password' => '$2y$10$d8JoZy3Ri0qUJFENnkKBvO8U8QUX/sFdrK72c/MAJ4m/XAM6GaX5u'),
            array('empid' => 'JP007', 'password' => '$2y$10$8PsVXvbqpSeHuX0F0/3tTeOnr8AyUwYiPwCxijc8OsCzaeH0/8XEW'),
            array('empid' => 'JP008', 'password' => '$2y$10$nUveSrUfGFj2UAAHGA.TQ.i8DSIgh44GfDHGPxIzEOkL.5f7Xi2gC'),
            array('empid' => 'PH001', 'password' => '$2y$10$yCM1r/1KudoJd8CoIZH47eL6VegPl.YqxN03/4nwSJ9cFJRt4M4Q.'),
            array('empid' => 'PH002', 'password' => '$2y$10$Z4p2x.YeRplA1XrFkNpTI.e91Evrlq5Re6UhIbOmY7TCOVa7sIxFK'),
            array('empid' => 'PH003', 'password' => '$2y$10$vIFsMUZIZGnNt/PqGHx8BObTlGOb110gcSnvRifdU2a5RdoHbBX66'),
            array('empid' => 'PH004', 'password' => '$2y$10$5lFaZynLFB..EdIwNeeXfeRB2L1FZNPfDly.2I.erCairykxGtDMi'),
            array('empid' => 'IN001', 'password' => '$2y$10$XN3.X2cYeADr9C2gJx9Km.skD03d0hnjRfzuVFD0VhQSzjsbA2Ysm'),
            array('empid' => 'IN002', 'password' => '$2y$10$g9wAaAlsWlQY2OzIvP5Wj.W5IbhTc2A.Zo8bdRXW8PcjWxdnPRDO6'),
            array('empid' => 'IN003', 'password' => '$2y$10$m6xazpkO.f4HSav4gk7rXekjFXFaIstqxMIaxEvL2dKDkjWXxiKEG'),
            array('empid' => 'IN005', 'password' => '$2y$10$i1sSGjNOWeqNrddFbEtqne3gFbJmRTgdqXsTQqbmFGeV6Vcff316e'),
            array('empid' => 'IN006', 'password' => '$2y$10$JKqyEA1SaZW9D49vwunFg.UHxmnYcFVVjEOew8wxRb0A70/KZzXbm'),
            array('empid' => 'IN007', 'password' => '$2y$10$ZY8unONYTwNRMdFEJIQajOTTnYS9hHHQABZcrdfGYw6imtS10Tvm.'),
            array('empid' => 'IN008', 'password' => '$2y$10$2dS5aJAy0f4iZRg4m6Z.CuPaQBBqvKX95VsF5SdJZJ1HYI5iKBYHq'),
            array('empid' => 'IN009', 'password' => '$2y$10$n/owVCq6I3fe2vZX3HBsa.TBq4NAZ5BnkxvNoemkT7yV1gzfCs/sy'),
            array('empid' => 'IN010', 'password' => '$2y$10$ELRDNCcnAC2Uyqc9v1ntOeGioLw2a1Yy5XTmsut4cvCXs9FwQnCQG'),
            array('empid' => 'IN011', 'password' => '$2y$10$mgHAgMOI.wjw5vSBi/PCo.ZLXP0nlBJeU37FmZejYNMmZzwy6W/IG'),
            array('empid' => 'IN012', 'password' => '$2y$10$M046l.9deosaaYnJGrmv5OqNb4iqR8HiMQGwaDcp36HzUGxQQjeWe'),
            array('empid' => 'IN013', 'password' => '$2y$10$CqY7pzCzO1e21CMYwpzH5udlvEXI9HVtHRnHIql6vc3yXGIJr4vym'),
            array('empid' => 'IN015', 'password' => '$2y$10$Ii2Y.hyOXLcF5D.4uWdLaOwyL8jeruOebS/K6iH6Gi/VaD7/Fyvg.'),
            array('empid' => 'IN016', 'password' => '$2y$10$qzrizx47F77br9PF9/xzhen/CfHUDUQAS90tsPYdEdFCYYu8Vlwoy'),
            array('empid' => 'IN017', 'password' => '$2y$10$8fLD4816hzXLaw7eZ/w35OvqDjUzydnuOLXPUhwzenHjpfTLRvryi'),
            array('empid' => 'IN018', 'password' => '$2y$10$WDx9eRlClyCYxbfzebXimuoHpwA4shjW9ljzp6QTXHusRlzQrvLlS'),
            array('empid' => 'IN019', 'password' => '$2y$10$T2ihT2iWe4U4yF76zRGfEeW4WGgit8krNuOJYjJhg8Pu182oTnCY2'),
            array('empid' => 'IN022', 'password' => '$2y$10$.5CebErG.6gC4sVfszFL8O6/zln.5X3eMgsEpiC3GZaeYp9gCI7OO'),
            array('empid' => 'IN023', 'password' => '$2y$10$ZlG./vw0xfgi5yzg6TJ06ea.qtNqvwh3Uj9eW5QozUmU5iT4O1PzO'),
            array('empid' => 'IN024', 'password' => '$2y$10$L4Ls3ZTBTV0QWZgcyowxXOtzfo.1Gkex0SR8YClQFz8rUbmtO9M3S'),
            array('empid' => 'IN025', 'password' => '$2y$10$W75uQIP/tAi1Jv5SPErpOuBE3luKD3lBSVXwPEWHvyNjxYox8Oazu'),
            array('empid' => 'IN026', 'password' => '$2y$10$9prmGk9dnbADC6t1Yon0SeKTWJilT0z1WxKiHEXVvzB4Sr6q34NYK'),
            array('empid' => 'IN029', 'password' => '$2y$10$XAVaRaSRrTHCdHxUrYZE5OYeumm0FRUr1tjoplVEGV6vqG8sDn1um'),
            array('empid' => 'IN031', 'password' => '$2y$10$3OysABVV2GkIf53nnn0EK.c/Ud3z/.SDwXvDAOUNW9F7OgJlNjIiy'),
            array('empid' => 'IN032', 'password' => '$2y$10$RLzUrOKTCBp7ctvNbZQDBuIk0azktT0q792FTFpsWdEhNyqivQsWq'),
            array('empid' => 'IN033', 'password' => '$2y$10$v3IMe/htUHPShccATeZkPuzDtTiTsEO2e7i5fxD/crraADR7jPckm'),
            array('empid' => 'IN034', 'password' => '$2y$10$CmLyL8WRVKT2hsobGCjKgOzUW5qizQbnXRCvz6rd8irtiMMTnNooG'),
            array('empid' => 'IN035', 'password' => '$2y$10$NbxIef4h5FYk5RyCCpl.te5Ymo.35vQsbkrMPI1fUjfnQd.Y4dDXW'),
            array('empid' => 'IN036', 'password' => '$2y$10$0D.BTYX5UEePpJfBYFnfquexqdU6BcXnSLfDuyjwWRY8v0ECCG26m'),
            array('empid' => 'IN037', 'password' => '$2y$10$iOFikKi6Fh8AORXKzIpWIOpekuaExrWpjiCVesmPZlvucO9i9mcjy'),
            array('empid' => 'IN038', 'password' => '$2y$10$0pqmAnChpIRw2MFiPNwsqOW9X0/6fVOEkZMtwrz/anXz2OHfjaLWO'),
            array('empid' => 'IN039', 'password' => '$2y$10$ATHcYyVwO/hD47ST9Gh6TuZSMZ8E3iIg0/MQFS2ApBXcquNNJreNG'),
            array('empid' => 'IN040', 'password' => '$2y$10$B3ak3bOgq1fjKgVHlOtbdO5ESgd9yeYPYyh8uX396lyLqeiEMLuUy'),
            array('empid' => 'IN041', 'password' => '$2y$10$hV.vN08wYCspznLe3r4fkOyAHSB06qBBfZVAD7MOKeV1wXnbm8NRC'),
            array('empid' => 'IN042', 'password' => '$2y$10$rqJF68b3PjceQYaUBHoBPuSFZZwd0zS4n3tDnqdr9ay1QRxAbn0ee'),
            array('empid' => 'IN043', 'password' => '$2y$10$42UMfURFjCKI6IZzAxGofuvOg2kQbYeIIOdrt9fgBc1DvJiFxSA.2'),
            array('empid' => 'IN044', 'password' => '$2y$10$JR4PW46um0fT4slVOgmiFuUoWQw.Dj31rRMglj8pbR2hRbXvqfFU.'),
            array('empid' => 'IN045', 'password' => '$2y$10$OvhNvaUr/s07u3mBhzV6s.tZzcxLU.7qS/uNLhejPKF2WmsxDpo0S'),
            array('empid' => 'IN049', 'password' => '$2y$10$nQkcsowtTIAhXuzkHkUemulOHai.rE/lB4HyrAQHBkbWz55CMtUgy'),
            array('empid' => 'IN046', 'password' => '$2y$10$Pif2.cQ3D1K3E/8bQKrW9uwrFgqvRIoT7i9OTnkgNSh159R8hUlli'),
            array('empid' => 'IN047', 'password' => '$2y$10$p.3Ka5xjTQv8NrYbzcxJwOeLg63h1cXLhgFAeHIF/ODyzg6tcgaBq'),
            array('empid' => 'IN048', 'password' => '$2y$10$HxxbdD9yatv4NHjvUYuFIeT63LNe/m/cTtyaCJ4uIQHJLGG3DP5vu'),
            array('empid' => 'IN051', 'password' => '$2y$10$gwdfGPLTX1LkTlSpfod76eNzTkCvbavaI/M/KfWoVk1gUqPJbeUt.'),
            array('empid' => 'IN054', 'password' => '$2y$10$a0QGeelsC.Z09iXqUYlaHOhSHugs8XAZERGqrDG9c7R90S.LxneEW'),
            array('empid' => 'IN055', 'password' => '$2y$10$3Gu9oOmpN3kkKNhwABIADeXXsUtvJXFx.eWQ7KyKg1rMWw6/wOlrC'),
            array('empid' => 'IN053', 'password' => '$2y$10$QEZdMyIIKNIa9Nimoiy.Xecvo.mGx4i2WvtLFxE.sccQhcGLQLNaS'),
        );
        foreach($passwords as $password) {
            DB::table('users')->where('employee_id', '=', $password['empid'])->update(['password' => $password['password']]);
        }
    }
}
